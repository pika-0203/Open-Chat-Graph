<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta')) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <!-- オープンチャット表示ヘッダー -->
    <article class="openchat unset">
        <header class="openchat-header description-close unset" id="openchat-header">
            <div class="talkroom_banner_img_area unset">
                <img class="talkroom_banner_img" alt="フラスコのイメージ" src="<?php echo fileUrl("/assets/labs.svg") ?>">
            </div>
            <h1 class="talkroom_link_h1 unset">Labs</span></h1>
            <div class="talkroom_description_box">
                <p id="talkroom-description" class="talkroom_description">試作中の機能をLabs（ラボ）として提供しています。</p>
            </div>
        </header>
    </article>
    <section class="openchat unset">
        <aside>
            <a href="/labs/live" class="labs-link unset">
                <h2>ライブトーク利用時間分析ツール</h2>
                <img src="<?php echo fileUrl("/images/livegraph.webp") ?>" alt="ライブトーク利用時間分析ツール">
            </a>
        </aside>
        <aside>
            <label for="joinCountCheckbox" class="labs-link unset">
                <input type="checkbox" id="joinCountCheckbox">
                <h2>グラフに参加数の推移を追加</h2>
                <img src="<?php echo fileUrl("/images/joinCount.webp") ?>" alt="グラフに参加数の推移を追加">
                <small style="color: black;">トーク履歴を読み込んで、参加人数の推移を表示できます。<br>チェックを入れると、各ページにファイル選択ボタンが表示されるようになります。</small>
                <br>
                <small style="font-size: 11px;">iOS・Android版LINEで保存されたテキスト形式のトーク履歴に対応しています。</small>
            </label>
        </aside>
    </section>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>

    <script src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <script type="module">
        import {
            CookieManager
        } from '<?php echo fileUrl("/js/CookieManager.js") ?>'

        const cookie = new CookieManager
    </script>
</body>

</html>