<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use Shadow\DB;

class RankingBanLabsPageController
{
    function index(?string $change)
    {
        $updatedAtValue = $change ? 1 : 0;

        $openChatList = DB::fetchAll(
            "SELECT
                oc.id,
                oc.name,
                oc.description,
                oc.local_img_url AS img_url,
                oc.emblem,
                oc.join_method_type,
                oc.category,
                oc.member,
                rb.member AS old_member,
                rb.datetime AS old_datetime,
                rb.end_datetime AS end_datetime,
                rb.percentage,
                rb.flag,
                rb.updated_at,
                rb.update_items
            FROM
                ranking_ban AS rb
                JOIN open_chat AS oc ON oc.id = rb.open_chat_id
            WHERE
                (rb.updated_at = 1
                OR rb.percentage <= 50)
                AND rb.updated_at >= {$updatedAtValue}
            ORDER BY
                `datetime` DESC,
                end_datetime DESC,
                percentage ASC
            LIMIT
                100"
        );

        $openChatList = array_map(function ($oc) {
            if (!$oc['update_items']) return $oc;
            $oc['update_items'] = array_keys(
                array_filter(json_decode($oc['update_items'], true))
            );
            return $oc;
        }, $openChatList);


        $_meta = meta();
        $_css = ['room_list', 'site_header', 'site_footer'];
        $_updatedAt = new \DateTime(file_get_contents(AppConfig::HOURLY_REAL_UPDATED_AT_DATETIME));
        $_now = file_get_contents(AppConfig::HOURLY_CRON_UPDATED_AT_DATETIME);

        return view(
            'ranking_ban_content',
            compact('_meta', '_css', 'openChatList', '_updatedAt', '_now')
        );
    }
}
