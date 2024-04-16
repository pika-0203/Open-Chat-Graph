<?php

declare(strict_types=1);

namespace App\Services\Recommend;

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
        'ゴルフ',
        '国家試験',
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
        'FX',
        '副業',
        '仮想通貨',
        '投資',
        'お金',
        'ポイ活',
        '株式投資',
        '占い師',
        'パチンコ・スロット（パチスロ）',
        'Coin',
    ];

    function getList(int $limit)
    {
        $hour = DB::fetchAll(
            "SELECT
                t2.tag
            FROM
                statistics_ranking_hour AS t1
                JOIN recommend AS t2 ON t1.open_chat_id = t2.id
            WHERE
                t1.diff_member >= 3
            ORDER BY
                t1.id ASC",
            args: [\PDO::FETCH_COLUMN, 0]
        );

        $hour24 = DB::fetchAll(
            "SELECT
                t2.tag
            FROM
                statistics_ranking_hour24 AS t1
                JOIN recommend AS t2 ON t1.open_chat_id = t2.id
            WHERE
                t1.diff_member >= 12
            ORDER BY
                t1.id ASC",
            args: [\PDO::FETCH_COLUMN, 0]
        );

        $filter = array_merge(RecommendPageList::TagFilter, self::ExtraTagFilter);
        $tags1 = array_filter(sortAndUniqueArray($hour), fn ($e) => $e && !in_array($e, $filter));
        $tags2 = array_filter(sortAndUniqueArray($hour24), fn ($e) => $e && !in_array($e, $filter) && !in_array($e, $tags1));

        return ['hour' => array_slice($tags1, 0, $limit), 'hour24' => array_slice($tags2, 0, $limit)];
    }
}
