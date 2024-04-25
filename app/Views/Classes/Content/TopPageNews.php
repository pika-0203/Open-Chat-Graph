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
                    データ取得手段の詳しい解説は<a href="/policy">オプチャグラフについて</a>のページをご覧ください。
                    ご質問やご不明点がある場合は、匿名コメント欄またはお問い合わせ窓口からお問い合わせください。',
                ]
            ),
            new self(
                new \DateTime('2024-04-25 09:00'),
                'タグの分類について',
                [
                    '【タグの分類について】
                    各ルームは、ルームのテーマにぴったり合う1つのタグで自動的に分類されます。例えば、美容室に関する話題のルームは「美容室」タグに分類され、「美容」とは分類されません。',
                    'タグの分類ロジックは、キーワードの一致だけでなく、文脈や異なる表現も考慮に入れて自動的に実行されます。これにより、ルームの内容を正確に反映したタグが付けられ、自分の興味や関心に合ったルームをより簡単に見つけることができます。',
                    '例えば、「ZB1、ゼロベースワン、ゼベワン」は全て同じタグに分類されます。ルームのタイトルが「雑談　ライブトーク　宣伝」のような複数のキーワードを使っている場合、キーワード毎の調整値に基づく優先度に応じて分類が変わります。',
                    'オプチャグラフはLINE公式サイトと同等の情報を掲載しており、15万件以上のオープンチャットを個別に分類してタグ付けし、それぞれのルームの増減数を1時間ごとに記録して、各タグの人気ランキングを算出しています。',
                    'オープンチャットの情報を掲載する仕組みにつきましては、<a href="/policy">オプチャグラフについて</a>をご覧ください。',
                    '【いま人数増加中のタグとは】
                    1時間毎の更新で、いまトレンドのテーマを表しています。',
                    '1つのタグのグループの中で、複数のルームが急増加している場合に浮上します。タグの並び順は、タググループ全体の急増度が高い順となります。',
                    '例えば、緑色の↑と枠が付く異常値のタグは、「過去1時間・過去24時間共に4人以上増加のルームが2つ以上」または「過去1時間で3人増加かつ過去24時間で20人以上増加のルームが4つ以上」の場合に表示されます。緑の枠がついてないタグは、異常値の条件を満たさない中で「過去24時間で10人以上増加のルーム」または「過去1週間で20人以上増加かつ減っていないルーム」が4つ以上の場合に表示されます。',
                    '条件に一致するタグは全て表示されます。この条件は機械学習(AI)を使い、過去の増加傾向からトレンドの始まりの傾向を算出したものです。条件は定期的に見直しされます。',
                    '企業毎の就活や新入社員のグループ・大学毎の新歓グループなど、ごく一部で表示対象外のタグがあります。',
                ]
            ),
        ];
    }
}
