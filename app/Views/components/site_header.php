<header class="site_header_outer" id="site_header">
    <div class="site_header">
        <a class="header_site_title unset" href="<?php echo url() ?>">
            <img src="<?php echo url(\App\Config\AppConfig::SITE_ICON_FILE_PATH) ?>" alt="">
            <?php if (strpos(path(), '/oc') === false) : ?>
                <h1>オプチャグラフ</h1>
            <?php else : ?>
                <p>オプチャグラフ</p>
            <?php endif ?>
        </a>

        <?php if (isset($_updatedAt)) : ?>
            <div class="refresh-time">
                <div class="refresh-icon"></div><time datetime="<?php echo $_updatedAt->format(\DateTime::ATOM) ?>"><?php echo $_updatedAt->format('Y/n/j G:i') ?></time>
            </div>
        <?php elseif (cookie()->has('admin')) : ?>
            <!-- admin用チェックボックス -->
            <label class="checkbox-label admin-check-label" for="adminEnable">
                <small>Admin</small>
                <input type="checkbox" id="adminEnable" <?php if (cookie()->has('admin-enable')) echo 'checked' ?>>
            </label>
            <script type="module">
                import {
                    AdminCheckboxCookieManager
                } from '<?php echo fileUrl("/js/AdminCheckboxCookieManager.js") ?>'

                const adminCookieManager = new AdminCheckboxCookieManager
            </script>
        <?php endif ?>

        <nav class="header-nav unset" style="height: 48px;">
            <button class="header-button unset" id="search_button" aria-label="検索">
                <span class="search-button-icon"></span>
            </button>
        </nav>
    </div>
    <div class="backdrop" id="backdrop" role="button" aria-label="閉じる"></div>
    <div class="search-form site_header">
        <form class="search-form-inner" method="GET" action="<?php echo url('ranking') ?>">
            <label for="q">
            </label>
            <input type="text" id="q" name="keyword" placeholder="オープンチャットを検索" maxlength="1000" autocomplete="off" required>
            <input type="hidden" name="list" value="all">
            <input type="hidden" name="sort" value="member">
            <input type="hidden" name="order" value="desc">
        </form>
    </div>
    <div class="header-ads">
        <span style="color: #aaa; font-size: 12px; position: absolute; right: 0; left: 0; text-align: center; top: 2.5rem;">advertisement</span>
        <ins class="adsbygoogle" style="display: block" data-ad-client="ca-pub-2330982526015125" data-ad-slot="8037531176" data-ad-format="horizontal" data-full-width-responsive="false"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
    </div>
    <script>
        // 現在の位置を保持
        let currentPosition = 0;

        // ヘッダーの高さを取得
        const header = document.querySelector(".site_header_outer");
        const headerHeight = header.clientHeight * -1;

        window.addEventListener("scroll", () => {
            // スクロール位置を保持
            let scrollPosition = document.documentElement.scrollTop;

            // スクロールに合わせて要素をヘッダーの高さ分だけ移動（表示域から隠したり表示したり）
            if (scrollPosition <= 0) {
                header.style.transform = "translate(0, 0)";
            } else if (currentPosition <= scrollPosition) {
                header.style.transform = "translate(0," + headerHeight + "px)";
            } else if (currentPosition > scrollPosition) {
                header.style.transform = "translate(0, 0)";
            }

            console.log(scrollPosition)
            currentPosition = document.documentElement.scrollTop;
        })
    </script>
</header>