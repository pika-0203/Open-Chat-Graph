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
                    ルーム・ランキング一覧やキーワード検索結果の画面に、並び替えの選択肢が追加されました。
                    「人数が少ない順」や「増加数が少ない順」といった新しい並び替えが可能になりました。
                    例えば、キーワード検索後の結果を人数が少ない順で並び替えるなどの表示ができるようになり、目的のルームを探しやすくなりました。
                    ',
                    '【ルーム作成日の表示機能を削除】
                    ルーム作成日の表示機能を削除しました。技術的な理解が必要なため、誤解を招く可能性があると判断したためです。
                    簡単に説明しますと、公式サイトから送信されてくるデータにはルーム作成日情報が含まれています。
                    しかしこれを画面に表示するかどうかは、使用する技術によって異なります。
                    一般のウェブサイトを見る際、表示の方法はユーザーが選ぶことができます。したがって、ルーム作成日を表示するかどうかは技術的な選択の範囲内だと言えます。
                    しかし、誤解を避けるという観点から、オプチャグラフでは現在表示していません。',
                ]
            ),
        ];
    }
}
