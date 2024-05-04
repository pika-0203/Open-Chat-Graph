<?php

declare(strict_types=1);

namespace App\Views\Content;

class LabsNews
{
    /** @return array{1:UpdateNews[]} */
    static function getNews(): array
    {
        return [
            new UpdateNews(
                new \DateTime('2024-05-05 04:28'),
                'オプチャ公式ランキング掲載の分析',
                [
                    '未掲載になった時点で、その原因がルーム変更によるものか識別可能になりました。
                    条件の絞り込み・ページ切り替えを実装しました。',
                ]
            ),
        ];
    }
}
