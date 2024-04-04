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
                <p>オプチャグラフはLINEオープンチャット公式サイトのデータを自動的に記録し、グラフ等に視覚化することで新たな知見を得ることができます。</p>
                <p>※オプチャグラフは個人開発によるLINE非公式のサービスです。
                    <br>
                    <span style="font-size: 12px; color: #777;">Gihub（ソースコード）: <a style="color: #777;" href="https://github.com/pika-0203/Open-Chat-Graph" target="_blank">https://github.com/pika-0203/Open-Chat-Graph</a></span>
                </p>
                <h2>サイトの目的</h2>
                <p>・ユーザーがオープンチャットを見つけて参加する機会を作る</p>
                <p>・オープンチャットの管理者が成長傾向を把握し、比較できる事で運営に役立つ</p>
                <h2>オプチャグラフ公開の経緯</h2>
                <p>
                    オプチャグラフの公開が可能になった経緯として、オプチャ公式による検索エンジンへの対応が始まった事があげられます。
                </p>
                <p>
                    オープンチャットのサービス開始当初、オープンチャットを検索したり、ランキングを見る機能はLINEアプリ内限定で提供されていました。
                </p>
                <p>
                    しかし、2023年10月頃に<a href="https://openchat.line.me/jp" target="_blank">「WEBブラウザ版メイン画面」（オプチャ公式サイト）</a>が公開された事により、LINEアプリ外のブラウザからオープンチャットを検索したり、ランキングを見ることが可能になりました。
                </p>
                <p>
                    「WEBブラウザ版メイン画面」に掲載されているオープンチャットが、検索エンジン（Googleなど）の検索結果に表示されることで、参加経路の拡大を図る目的があるようです。
                </p>
                <p>
                    これは <a href="https://ja.wikipedia.org/wiki/%E6%A4%9C%E7%B4%A2%E3%82%A8%E3%83%B3%E3%82%B8%E3%83%B3%E6%9C%80%E9%81%A9%E5%8C%96" target="_blank">SEO</a> と言われるマーケティングの一環により、効果的に検索エンジンへの掲載を図り、ユーザーの認知を増やすことを目的とした媒体であると考えられます。
                </p>
                <p>
                    <b>オプチャグラフは「WEBブラウザ版メイン画面」の分析データを掲載し、参加経路拡大に寄与するために開発されたオープンチャット専用の検索エンジンです。</b>
                    <br>
                    オプチャ公式サイトが送信しているデータを、Google・Bingなどの検索エンジンと同様の一般的なルールに基づいて公開しています。
                </p>
                <p>
                    オープンチャットのデータを不正に公開するものではなく、適切な範囲内で健全な情報共有を行うWEBサイトです。
                </p>
                <p>
                    例えば、ランキングの順位はデータの並び順をカウントしてそのまま順位として表示したものであり、誰でも公式サイトで見ることができる情報に過ぎません。
                </p>
                <p>
                    <br>
                    オープンチャットの参加経路拡大に寄与するため、オプチャグラフもSEOを考慮した設計を行っています。
                </p>
                <p>
                    参考ページ: <a href="https://openchat-jp.line.me/other/notice_webmain_3gf87gs1" target="_blank">Webブラウザ版メイン画面公開と検索エンジン対応のお知らせ | LINEオープンチャット</a>
                </p>
                <h2>オープンチャットの掲載条件</h2>
                <p>LINEオープンチャット公式サイトに掲載されているランキングを基にオープンチャットの情報を記録して掲載します。</p>
                <p>公式サイトにて掲載が終了したオープンチャットはオプチャグラフからも削除されます。</p>
                <p style="font-size: 12px; color: #777;">データ収集は公式サイトの robots.txt を尊重して行います。<br>LINE側の公式サイトによるオープンチャットの掲載条件は非公開となっております。</p>
                <p style="font-size: 12px; color: #777;">LINEオープンチャット公式サイト: <a href="https://openchat.line.me/jp" target="_blank">https://openchat.line.me/jp</a></p>
                <p style="font-size: 12px; color: #777;">LINEオープンチャット利用規約: <a href="https://terms.line.me/line_Square_TOU_JP?lang=ja" target="_blank">https://terms.line.me/line_Square_TOU_JP?lang=ja</a></p>
                <h2 style="margin-bottom: 2rem;">オプチャグラフに関する情報共通・連絡掲示板</h2>
                <div id="comment-root"></div>
                <br>
                <p style="font-size: 12px; color: #777;">オプチャグラフお問い合わせ窓口: <a href="mailto:support@openchat-review.me">support@openchat-review.me</a></p>
            </article>
        </main>
        <footer>
            <?php viewComponent('footer_inner') ?>
        </footer>
    </div>
    <?php echo $_breadcrumbsShema ?>
</body>

</html>