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
        'FX',
        '副業',
        'せどり',
        '医療',
        '数学',
        '通信',
        '投資',
        '就活',
        '薬学',
        '試験',
        'ゴルフ',
        '国家試験',
        '化学',
        '勉強',
        '中国語',
        '株式投資',
        '受験',
        'Coin',
        '起業',
        'プロ野球',
        '野球',
        '仮想通貨',
        '不動産',
        '経済',
        '美容',
        'ヘア',
        'お金',
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
                statistics_ranking_hour24 AS t1
                JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                JOIN oc_tag AS t3 ON t1.open_chat_id = t3.id
                JOIN oc_tag2 AS t4 ON t1.open_chat_id = t4.id
            WHERE
                t1.diff_member >= 5
            ORDER BY
                t1.id ASC"
        );

        $tags = array_column($tags, 'tag1');

        $filter = array_merge(RecommendOpenChatPageController::TagFilter, self::ExtraTagFilter);

        $tags = array_filter($tags, fn ($e) => !in_array($e, $filter));
        $tags = sortAndUniqueArray($tags);
        $tags = array_slice($tags, 0, 40);
        shuffle($tags);
        return array_slice(sortAndUniqueArray($tags), 0, $limit + 1);
    }
}
