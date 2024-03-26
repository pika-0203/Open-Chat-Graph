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
                <h2>サイトの目的</h2>
                <p>・オープンチャットの管理者が成長傾向を把握し、比較できる事で運営に役立つ</p>
                <p>・ユーザーがオープンチャットを見つけて参加する機会を作る</p>
                <h2>オープンチャットの掲載条件</h2>
                <p>LINEオープンチャット公式サイトに掲載されているランキングを基にオープンチャットを記録します。<br>掲載が終了したオープンチャットはオプチャグラフから削除されます。</p>
                <p style="font-size: 11px; color: #b7b7b7;">公式サイトからのデータ収集は robots.txt を尊重して行います。公式サイトでの掲載条件はオープンチャットの利用規約などLINEの情報をご確認ください。</p>
                <p style="font-size: 11px; color: #b7b7b7;">LINEオープンチャット公式サイト: <a rel="external nofollow noopener" href="https://openchat.line.me/jp" target="_blank">https://openchat.line.me/jp</a></p>
                <p style="font-size: 11px; color: #b7b7b7;">LINEオープンチャット利用規約: <a rel="external nofollow noopener" href="https://terms.line.me/line_Square_TOU_JP?lang=ja" target="_blank">https://terms.line.me/line_Square_TOU_JP?lang=ja</a></p>
                <h2 style="border:none; margin-bottom:0;">コメント欄</h2>
                <div id="comment-root" style="margin: 0;"></div>
                <br>
                <p style="font-size: 11px; color: #b7b7b7;">オプチャグラフお問い合わせ窓口: <a href="mailto:support@openchat-review.me">support@openchat-review.me</a></p>
                <p style="font-size: 11px; color: #b7b7b7;">Gihub: <a rel="external nofollow noopener" href="https://github.com/pika-0203/Open-Chat-Graph" target="_blank">https://github.com/pika-0203/Open-Chat-Graph</a></p>
            </article>
        </main>
        <footer>
            <?php viewComponent('footer_inner') ?>
        </footer>
    </div>
    <?php echo $_breadcrumbsShema ?>
</body>

</html>