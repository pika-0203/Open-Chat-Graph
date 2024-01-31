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
    </section>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>

    <script src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
</body>

</html>