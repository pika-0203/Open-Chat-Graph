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

        <?php if (cookie()->has('admin')) : ?>
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

        <nav class="header-nav unset">
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
            <input type="text" id="q" name="keyword" placeholder="オープンチャットを検索" maxlength="40" autocomplete="off" required>
            <input type="hidden" name="list" value="all">
            <input type="hidden" name="sort" value="member">
            <input type="hidden" name="order" value="desc">
        </form>
    </div>
</header>