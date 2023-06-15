<header class="site_header_outer" id="site_header">
    <div class="site_header">
        <a class="header_site_title unset" href="<?php echo url() ?>">
            <img src="<?php echo url('assets/icon-192x192.webp') ?>" alt="">
            <?php if (strpos(path(), '/oc') === false) : ?>
                <h1>オプチャグラフ</h1>
            <?php else : ?>
                <p>オプチャグラフ</p>
            <?php endif ?>
        </a>
        <nav class="header-nav unset">
            <button class="header-button unset" id="search_button" aria-label="検索">
                <span class="search-button-icon"></span>
            </button>
        </nav>
    </div>
    <div class="backdrop" id="backdrop" role="button" aria-label="閉じる"></div>
    <div class="search-form site_header">
        <form class="search-form-inner" method="GET" action="<?php echo url('search') ?>">
            <label for="q">
            </label>
            <input type="text" id="q" name="q" placeholder="オープンチャットを検索" maxlength="40" autocomplete="off" required>
        </form>
    </div>
</header>