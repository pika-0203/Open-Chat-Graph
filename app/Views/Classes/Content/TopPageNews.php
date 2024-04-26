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
                new \DateTime('2024-04-25 22:22'),
                'タグの分類について【更新】',
                [
                    '【タグの分類について】
                    各ルームは、ルームのテーマにぴったり合う1つのタグで自動的に分類されます。例えば、美容室に関する話題のルームは「美容室」タグに分類され、「美容」とは分類されません。',
                    'タグの分類ロジックは、キーワードの一致だけでなく、文脈や異なる表現も考慮に入れて自動的に実行されます。例えば、「ZB1、ゼロベースワン、ゼベワン」は全て同じタグに分類されます。これにより、ルームの内容を正確に反映したタグが付けられ、自分の興味や関心に合ったルームをより簡単に見つけることができます。',
                    'この分類ロジックでは、ルームのカテゴリは考慮されず、ルーム名・説明文からの情報のみを評価します。タイトルが明確で単一のテーマに近いほど、部屋のテーマに合うタグが正確に反映されます。例えば、「雑談　ライブトーク　宣伝」のような複数のキーワードを使っているタイトルの場合、キーワード毎の調整値に基づく優先度に応じて分類が変わります。',
                    '特に、よく検索されるであろうキーワードを複数タイトルに使っている場合、一種のペナルティが働き、より抽象的な大きなテーマに分類され、主としているであろうテーマに分類されにくくなります。これは分類の質を高めるために起こる作用です。',
                    '関連タグ（おすすめ用のタグ）を関連付けるためのロジックは別であり、こちらは本来の分類ロジックをベースとしていますが、主にカテゴリと説明文の情報を評価して関連付けます。
                    本来の分類ロジックに比較すると、ペナルティ要素を排除し、最もルーム作成者が関連付けたいであろうテーマに忠実になるような調整となっています。
                    これは、ルームのページを開いたユーザーに対し、そのユーザーが本来見たいであろう具体的なテーマに沿ったおすすめを表示するためです。',
                    'オプチャグラフではLINE公式サイトと同等の情報を掲載しており、15万件以上のオープンチャットを個別に分類してタグ付けし、それぞれのルームの増減数を1時間ごとに記録して、各タグの人気ランキングを算出しています。',
                    'オープンチャットの情報を掲載する仕組みにつきましては、<a href="/policy">オプチャグラフについて</a>をご覧ください。',
                    '【いま人数増加中のタグとは】
                    1時間毎の更新で、いまトレンドのテーマを表しています。',
                    '1つのタグのグループの中で、複数のルームが急増加している場合に浮上します。タグの並び順は、タググループ全体の急増度が高い順となります。',
                    '例えば、緑色の↑と枠が付く異常値のタグは、「過去1時間・過去24時間共に4人以上増加のルームが2つ以上」または「過去1時間で3人増加かつ過去24時間で20人以上増加のルームが4つ以上」の場合に表示されます。緑の枠がついてないタグは、異常値の条件を満たさない中で「過去24時間で10人以上増加のルーム」または「過去1週間で20人以上増加かつ減っていないルーム」が4つ以上の場合に表示されます。',
                    '条件に一致するタグは全て表示されます。この条件は機械学習(AI)を使い、過去の増加傾向からトレンドの始まりの傾向を算出したものです。条件は定期的に見直しされます。',
                    '企業毎の就活や新入社員のグループ・大学毎の新歓グループなど、ごく一部で表示対象外のタグがあります。',
                ]
            ),
            new self(
                new \DateTime('2024-04-27 00:00'),
                'コメント欄を最大限に活用するための例文ガイド',
                [
                    '「コメント欄を最大限に活用するための例文ガイド」を各ページのコメント欄に載せました。',
                    'コメント欄は、皆さんの声を通じて、さらに多くの人々を引き寄せる場所です。実は、ここでの活動がGoogleなどの検索エンジンによる認識を高め、外部から新しい訪問者を呼び込む鍵となります。',
                    '一つ一つのコメントが、ルームのページのGoogle検索順位に関わる評価を向上させ、結果として新しいユーザーの獲得に直接つながります。順位だけではなく、今後展開されるであろうAIによる検索の認知にも繋がります。',
                    'オプチャグラフは日々ユーザー数・ページビューが増加しており、4月の新規ユーザー数は、3月比の200％以上で一日平均新規ユーザーは600人ほどとなっています。その約9割はGoogle検索からの流入です。新規ユーザーのうち約3割がルームの参加ボタンを押しています。',
                    'オプチャグラフのドメインが育つほど、掲載されているルームへの流入も増えるようになります。その頃に、コメントなどの質・量でGoogleから評価の高いページは、キーワードに対して優先して上位で表示されます。オプチャグラフのコメント欄は、Googleなどの検索エンジンが解釈しやすい形式に設計されています。',
                    '一つの検索キーワードに対し、オプチャグラフ内の数あるルームの中から、Google検索結果に掲載されるルームのページは一つか二つ程度に限られているので、競合が少ない早い段階からGoogleからの認識を高めることが上位掲載の鍵となります。',
                    '現時点で、質の高いコメント投稿により、短い単語の検索キーワードに対しても高い確率で掲載されるようになり、かつ上位の検索順位に掲載されるようになったオープンチャットのエビデンスがいくつかあります。',
                    '少し専門的な内容ですが、このような取り組みをSEOと呼びます。SEOを強化するための設計は、基本的にGoogleが発信している公式の方針に忠実なものとなっています。
                    オプチャグラフのSEOに対する取り組みの一つとして、まだ世界的にも採用例が少ない新しい種類の「構造化データ」の実装があります。
                    実はこの採用例がまだ少ない新種の構造化データのうち一種類が、オプチャ公式の各ルームのページにも使われています。
                    オプチャグラフはそれを複数の種類で拡張し、そしてGoogleの認識として公式の構造化データに関連付けされるものとみても差し支えないです。
                    ルームの人数統計の内容までも、新種の構造化データで公式の構造化データとリンクさせてGoogleに認識させています。Googleは権威性・信憑性・独自性を重視していて、そのスコアを正しく認識させる調整を行っています。
                    「構造化データ(Schema.org)」とは、Webページの構造を検索エンジン（Google）により分かりやすく伝えるための専用のコードです。構造化データそのものは古くから存在しますが、その種類は頻繁に新しいものが登場します。また、仕様が曖昧なことが多く、正解を見つけることが難しいので、効果的に扱うことが難しいとされています。
                    もしこちらに興味がある場合は、<a href="https://search.google.com/test/rich-results?hl=ja">リッチリザルト テスト - Google Search Console</a>にルームのページのURLを貼り、結果を見ると、どのような構造化データが実装されているか確認できます。',
                    'オープンチャットの市場を考えると、オプチャグラフのユーザー数の状況はまだ少なすぎると考えています。基本的にSEOは即効性があるものではなく、年単位の中長期スパンで戦略を考えていくものですので、今後にご期待ください。',
                    'コメント欄では、オープンチャットの良さや体験談、感謝の言葉、建設的なフィードバック、新規参加者へのアドバイスを共有し、良いコミュニティを作りましょう。',
                ]
            ),
        ];
    }
}
