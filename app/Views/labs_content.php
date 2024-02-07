<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta')) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <!-- オープンチャット表示ヘッダー -->
    <article class="openchat unset" style="margin-bottom: -3rem;">
        <header class="openchat-header unset">
            <div class="talkroom_banner_img_area unset">
                <img class="talkroom_banner_img" alt="フラスコのイメージ" src="<?php echo fileUrl("/assets/labs.svg") ?>">
            </div>
            <div class="talkroom_description_box">
                <h2 class="talkroom_link_h1 unset">Labs</span></h2>
                <p id="talkroom-description" class="talkroom_description">試作中の機能をLabs（ラボ）として提供しています。</p>
            </div>
        </header>
    </article>
    <section style="margin-bottom: 3rem;">
        <aside>
            <a href="/labs/live" class="labs-link unset">
                <h3>ライブトーク分析</h3>
                <p><small>ライブトークの利用時間をグラフ表示します</small></p>
                <img src="<?php echo fileUrl("/images/livegraph.webp") ?>" alt="ライブトーク利用時間分析ツール">
                <br>
            </a>
        </aside>
    </section>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>

    <script src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
</body>

</html>