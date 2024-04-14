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
        '韓国',
        '韓国語',
        '占い師',
        'パチンコ・スロット（パチスロ）',
        'Coin',
    ];

    function getList(int $limit)
    {
        $tags = DB::fetchAll(
            "SELECT
                t2.tag
            FROM
                statistics_ranking_hour AS t1
                JOIN recommend AS t2 ON t1.open_chat_id = t2.id
            WHERE
                t1.diff_member >= 3
            ORDER BY
                t1.id ASC"
        );

        $tags = array_column($tags, 'tag');

        $filter = array_merge(RecommendOpenChatPageController::TagFilter, self::ExtraTagFilter);

        $tags = sortAndUniqueArray($tags);
        $tags = array_filter($tags, fn ($e) => $e && !in_array($e, $filter));

        return array_slice($tags, 0, $limit);
    }
}
