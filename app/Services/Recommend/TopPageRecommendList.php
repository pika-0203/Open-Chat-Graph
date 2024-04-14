<?php

declare(strict_types=1);

namespace App\Services\Recommend;

use App\Controllers\Pages\RecommendOpenChatPageController;
use Shadow\DB;

class TopPageRecommendList
{
    const ExtraTagFilter = [
        '大学新入生同士の情報交換',
        '大学 新入生',
        '大学',
        '就活生情報・選考対策・企業研究',
        '国語',
        '25卒',
        '26卒',
        '27卒',
        'ロック',
        '28卒',
        '29卒',
        '24卒',
        '23卒',
        '医療',
        '通信',
        '投資',
        '薬学',
        '試験',
        'ゴルフ',
        '国家試験',
        '化学',
        '勉強',
        '中国語',
        '受験',
        '起業',
        'プロ野球',
        '車',
        '野球',
        '不動産',
        '経済',
        '美容',
        'ヘア',
        '競艇予想',
        '競馬予想',
        'サークル',
    ];

    function getList(int $limit)
    {
        $tags = DB::fetchAll(
            "SELECT
                t2.tag AS tag1,
                t3.tag AS tag2,
                t4.tag AS tag3
            FROM
                statistics_ranking_hour AS t1
                JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                JOIN oc_tag AS t3 ON t1.open_chat_id = t3.id
                JOIN oc_tag2 AS t4 ON t1.open_chat_id = t4.id
            WHERE
                t1.diff_member >= 3
            ORDER BY
                t1.id ASC"
        );

        $tags = array_merge(
            array_column($tags, 'tag1'),
        );

        $filter = array_merge(RecommendOpenChatPageController::TagFilter, self::ExtraTagFilter);

        $tags = array_filter($tags, fn ($e) => !in_array($e, $filter) && $e);
        $tags = array_filter($tags, fn ($e) => !str_contains($e, '限定'));
        $tags = array_filter($tags, fn ($e) => !str_contains($e, '学生'));
        $tags = sortAndUniqueArray($tags);
        $tags = array_slice($tags, 0, 50);

        shuffle($tags);
        return array_slice($tags, 0, $limit);
    }
}
