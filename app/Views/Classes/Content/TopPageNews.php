<?php

declare(strict_types=1);

namespace App\Views\Content;

class TopPageNews
{
    /** @var string[]|string[][] $body */
    function __construct(
        public \DateTime $date,
        public string $title,
        public array $body,
    ) {
    }

    /** @return array{1:TopPageNews[]} */
    static function getTopPageNews(): array
    {
        return [
            new self(
                new \DateTime('2024-04-21 23:00'),
                'アップデート',
                [
                    '【ルームの並び順に少ない順を追加】
                    ルーム一覧画面に、並び替えの選択肢が追加されました。「人数が少ない順」や「増加数が少ない順」といった新しい並び替えが可能になりました。例えば、キーワード検索後の結果を人数が少ない順で並び替えるなどの表示ができるようになり、目的のルームを探しやすくなりました。',
                    '【ルーム作成日の表示機能を削除】
                    オプチャグラフのデータ取得手段に関して技術的な混乱や誤解を避けるため、ルーム作成日の表示機能を削除しました。公式サイトから得られるデータには作成日が含まれますが、この情報の表示はデータの表示方法によって変わり、通常のブラウザ画面からは見えないものです。これが利用者に間違った理解を与える可能性があるため、表示を行わないことにしました。
                    データ取得手段の詳しい解説は「オプチャグラフについて」のページをご覧ください。
                    ご質問やご不明点がある場合は、匿名コメント欄またはメールからお問い合わせください。',
                ]
            ),
        ];
    }
}
