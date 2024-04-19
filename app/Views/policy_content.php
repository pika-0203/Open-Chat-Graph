<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('policy_head', compact('_css', '_meta')) ?>

<body>
    <script type="application/json" id="comment-app-init-dto">
        <?php echo json_encode(['openChatId' => 0, 'baseUrl' => url()], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
    </script>
    <div class="body">
        <?php viewComponent('site_header') ?>
        <main>
            <article class="terms">
                <h1 style="letter-spacing: 0px;">オプチャグラフについて</h1>
                <p>オプチャグラフは興味があるオープンチャットを見つけて、成長傾向をグラフで見ることができる場所です。</p>
                <p>※オプチャグラフは個人開発によるLINE非公式のサービスです。 </p>
                <h2>サイトの目的</h2>
                <p>・ユーザーがオープンチャットを見つけて参加する機会を作る</p>
                <p>・オープンチャットの管理者が成長傾向を把握し、比較できる事で運営に役立つ</p>
                <h2>オープンチャットの情報を掲載する仕組み</h2>
                <p>
                    オプチャグラフでは<a href="https://openchat.line.me/jp/explore?sort=RANKING" rel="external" target="_blank">公式ランキング（LINE公式サイト）</a>から一般的な検索エンジン（Google・Bing等）と同じ方法でオープンチャットのデータをインデックス（記録）し、視覚的に見やすいグラフやランキング形式に表して表示しています。
                    <br>公式サイトにて掲載が終了・削除されたオープンチャットはオプチャグラフからも48時間以内に削除されます。
                </p>
                <p style="font-size: 12px; color: #777;">LINE公式サイトでの掲載条件は非公開となっています。</p>
                <p><a href="<?php echo url('oc') ?>">オープンチャットを手動で登録する</a></p>
                <p><a href="<?php echo url('recently-registered') ?>">最近登録されたオープンチャット</a></p>
                <h2>オプチャグラフ公開の経緯</h2>
                <p>
                    オプチャグラフの公開が可能になった経緯として、オプチャ公式による検索エンジンへの対応が始まった事があげられます。
                    オープンチャットのサービス開始当初、オープンチャットを検索したり、ランキングを見る機能はLINEアプリ内限定で提供されていました。
                </p>
                <p>
                    しかし、2023年10月頃に<a href="https://openchat.line.me/jp" target="_blank">「WEBブラウザ版メイン画面」（オプチャ公式サイト）</a>が公開された事により、LINEアプリ外のブラウザからオープンチャットを検索したり、ランキングを見ることが可能になりました。「WEBブラウザ版メイン画面」に掲載されているオープンチャットが、検索エンジン（Googleなど）の検索結果に表示されることで、参加経路の拡大を図る目的があるようです。
                    <a href="https://ja.wikipedia.org/wiki/%E6%A4%9C%E7%B4%A2%E3%82%A8%E3%83%B3%E3%82%B8%E3%83%B3%E6%9C%80%E9%81%A9%E5%8C%96" target="_blank">SEO</a>と言われるマーケティングの一環により、効果的に検索エンジンへの掲載を図りユーザーの認知を増やすことを目的とした媒体であると考えられます。
                </p>
                <p>
                </p>
                <p>
                    <b>オプチャグラフは「WEBブラウザ版メイン画面」の分析データを掲載し、参加経路拡大に寄与するために開発されたオープンチャット専用の検索エンジンです。</b>
                    オプチャ公式サイトが送信しているデータを、Google・Bingなどの検索エンジンと同様の一般的なルールに基づいて公開しています。
                </p>
                <p>
                    オープンチャットのデータを不正に公開するものではなく、適切な範囲内で健全な情報共有を行うWEBサイトです。
                    例えば、ランキングの順位はデータの並び順をカウントしてそのまま順位として表示したものであり、誰でも公式サイトで見ることができる情報に過ぎません。
                </p>
                <p>
                    オープンチャットの参加経路拡大に寄与するため、オプチャグラフもSEOを考慮した設計を行っています。
                </p>
                <p>
                    参考ページ: <a href="https://openchat-jp.line.me/other/notice_webmain_3gf87gs1" target="_blank">Webブラウザ版メイン画面公開と検索エンジン対応のお知らせ | LINEオープンチャット</a>
                </p>
                <p style="font-size: 12px; color: #777;">LINEオープンチャット利用規約: <a href="https://terms.line.me/line_Square_TOU_JP?lang=ja" target="_blank">https://terms.line.me/line_Square_TOU_JP?lang=ja</a>
                    <br>
                    データ収集は公式サイトの robots.txt を尊重して行います。
                </p>
                <h2 style="margin-bottom: 2rem;" id="comments">オプチャグラフに関する情報共有・コメント</h2>
                <div id="comment-root"></div>
                <h2>お問い合わせ・ソースコード</h2>
                <p style="font-size: 12px; color: #777;">オプチャグラフお問い合わせ窓口: <a href="mailto:support@openchat-review.me">support@openchat-review.me</a></p>
                <p>
                    <span style="font-size: 12px; color: #777;">Gihub バックエンド: <a style="color: #777;" href="https://github.com/pika-0203/Open-Chat-Graph" target="_blank">https://github.com/pika-0203/Open-Chat-Graph</a></span>
                    <br>
                    <span style="font-size: 12px; color: #777;">Gihub バックエンドFW: <a style="color: #777;" href="https://github.com/mimimiku778/MimimalCMS" target="_blank">https://github.com/mimimiku778/MimimalCMS</a></span>
                    <br>
                    <span style="font-size: 12px; color: #777;">Gihub フロント(ランキングページ): <a style="color: #777;" href="https://github.com/mimimiku778/Open-Chat-Graph-Frontend" target="_blank">https://github.com/mimimiku778/Open-Chat-Graph-Frontend</a></span>
                    <br>
                    <span style="font-size: 12px; color: #777;">Gihub フロント(グラフ表示): <a style="color: #777;" href="https://github.com/mimimiku778/Open-Chat-Graph-Frontend-Stats-Graph" target="_blank">https://github.com/mimimiku778/Open-Chat-Graph-Frontend-Stats-Graph</a></span>
                    <br>
                    <span style="font-size: 12px; color: #777;">Gihub フロント(コメント機能): <a style="color: #777;" href="https://github.com/mimimiku778/Open-Chat-Graph-Comments" target="_blank">https://github.com/mimimiku778/Open-Chat-Graph-Comments</a></span>
                </p>
            </article>
        </main>
        <footer>
            <?php viewComponent('footer_inner') ?>
        </footer>
    </div>
    <?php echo $_breadcrumbsShema ?>
</body>

</html>