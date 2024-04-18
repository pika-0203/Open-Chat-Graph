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
        '28卒',
        '29卒',
        '24卒',
        '23卒',
        '医療',
        '通信',
        '新入生',
        '受験',
        '経済',
        '美容',
        'ヘア',
        'FX',
        'サークル',
        '新歓',
        '占い師',
        'パチンコ・スロット（パチスロ）',
        '翻訳',
    ];

    function getList(int $limit)
    {
        $hour = DB::fetchAll(
            "SELECT
                t2.tag
            FROM
                statistics_ranking_hour AS t1
                JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                LEFT JOIN statistics_ranking_hour24 AS t3 ON t3.open_chat_id = t1.open_chat_id
            WHERE
                t1.diff_member >= 4 AND t3.diff_member >= 4
            ORDER BY
                t1.id ASC",
            args: [\PDO::FETCH_COLUMN, 0]
        );

        $hour2 = DB::fetchAll(
            "SELECT
                t2.tag
            FROM
                statistics_ranking_hour AS t1
                JOIN recommend AS t2 ON t1.open_chat_id = t2.id
                LEFT JOIN statistics_ranking_hour24 AS t3 ON t3.open_chat_id = t1.open_chat_id
            WHERE
                t1.diff_member >= 3
                AND t3.diff_member >= 20
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
                LEFT JOIN statistics_ranking_week AS t3 ON t3.open_chat_id = t1.open_chat_id
            WHERE
                t1.diff_member >= 10
                OR (t3.diff_member >= 20 AND t1.diff_member >= 0)
                OR (t3.diff_member >= 50)
            ORDER BY
                t1.id ASC",
            args: [\PDO::FETCH_COLUMN, 0]
        );

        $filter = array_merge(RecommendPageList::TagFilter, self::ExtraTagFilter);
        
        $tags = array_filter(sortAndUniqueArray($hour), fn ($e) => $e && !in_array($e, $filter));
        $tags1 = array_filter(sortAndUniqueArray($hour2, 4), fn ($e) => $e && !in_array($e, $filter) && !in_array($e, $tags));
        $hourTags = array_merge($tags, $tags1);

        $tags2 = array_filter(sortAndUniqueArray($hour24, 4), fn ($e) => $e && !in_array($e, $filter) && !in_array($e, $hourTags));

        return ['hour' => $hourTags, 'hour24' => array_slice($tags2, 0, $limit)];
    }
}
