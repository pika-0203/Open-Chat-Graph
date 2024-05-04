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
                    '未掲載になった時点で、その原因がルーム変更によるものか識別可能になりました。',
                    'このアップデートに伴い、掲載履歴のデータが一旦全てリセットされました。
                    2024/05/05 04:30 以降、ルーム内容変更の履歴につきましては、未掲載・再掲載の履歴と共に1時間毎に記録されます。'
                ]
            ),
        ];
    }
}
