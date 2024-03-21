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
                <p>オプチャグラフは<a href="https://openchat.line.me/jp" rel="external" target="_blank">LINEオープンチャット公式サイト</a>のデータを自動的に記録し、グラフ等に視覚化することで新たな知見を得ることができます。</p>
                <h2>サイトの目的</h2>
                <p>・オープンチャットの管理者が成長傾向を把握し、比較できる事で運営に役立つ</p>
                <p>・ユーザーがオープンチャットを見つけて参加する機会を作る</p>
                <h2>オープンチャットの掲載条件</h2>
                <p>公式サイトで表示されているランキングのデータを基に掲載します。</p>
                <p>公式サイトにて掲載が終了したオープンチャットはオプチャグラフから削除されます。</p>
                <!-- <p>オープンチャットがオプチャグラフに表示されないようにするには、オープンチャットの説明文に #nolog を含めます。</p>
                <p>既に登録済みのオープンチャットはデータ更新時に #nolog を検出すると削除されます。</p> -->
                <h2>コメント欄</h2>
                <div id="comment-root" style="margin: 1rem 0;"></div>
                <h2>お問い合わせ窓口</h2>
                <p>E-mail: <a href="mailto:support@openchat-review.me">support@openchat-review.me</a></p>
                <h2>リンク</h2>
                <p>Gihub: <a rel="external nofollow noopener" href="https://github.com/pika-0203/Open-Chat-Graph" target="_blank">https://github.com/pika-0203/Open-Chat-Graph</a></p>
            </article>
        </main>
        <footer>
            <?php viewComponent('footer_inner') ?>
        </footer>
    </div>
</body>

</html>