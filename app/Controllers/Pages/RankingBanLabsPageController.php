<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use Shadow\DB;

class RankingBanLabsPageController
{
    function index(?string $change, ?string $publish, ?string $order)
    {
        $updatedAtValue = "AND (rb.updated_at >= 1 OR (rb.update_items IS NOT NULL AND rb.update_items != ''))";
        if ($change === '1') $updatedAtValue = "AND (rb.updated_at = 0 AND (rb.update_items IS NULL OR rb.update_items = ''))";

        $endDatetime = "AND rb.end_datetime IS NOT NULL";
        if ($publish === '1') $endDatetime = "AND rb.end_datetime IS NULL";

        $per = 50;
        if ($order === '1') $per = 80;
        if ($order === '2') $per = 100;

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
                rb.percentage < {$per}
                {$updatedAtValue}
                {$endDatetime}
            ORDER BY
                GREATEST(`datetime`, `end_datetime`) DESC,
                `datetime` DESC,
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


        $_meta = meta()->setTitle('オプチャ公式ランキング掲載の分析')->setDescription('オプチャ公式ランキングへの掲載・未掲載の状況を一覧表示します。ルーム内容の変更後などに起こる掲載状況（検索落ちなど）の変動を捉えることができます。');
        $_css = ['room_list', 'site_header', 'site_footer'];
        $_updatedAt = new \DateTime(file_get_contents(AppConfig::HOURLY_REAL_UPDATED_AT_DATETIME));
        $_now = file_get_contents(AppConfig::HOURLY_CRON_UPDATED_AT_DATETIME);

        return view(
            'ranking_ban_content',
            compact('_meta', '_css', 'openChatList', '_updatedAt', '_now')
        );
    }
}
