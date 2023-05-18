<header class="site_header" id="site_header">
    <a class="header_site_title unset" href="<?php echo url() ?>">
        <img src="<?php echo url('assets/icon-192x192.webp') ?>" alt="">
        <p>オプチャグラフ</p>
    </a>
    <nav class="header-nav unset">
        <button class="header-button" id="search_button" aria-label="検索">
            <span class="search-button-icon"></span>
        </button>
        <?php if (\App\Services\Auth::check()) : ?>
            <button class="header-button" id="menu_button" aria-label="メニュー">
                <span class="menu-button-icon" aria-hidden="true"></span>
            </button>
            <div class="user-menu">
                <div class="user-menu-inner" id="user_menu">
                    <span class="user-menu-item user-menu-header">
                        <span class="user-menu-status">LINEでログイン済み</span>
                        <span class="user-menu-id">ID: <?php echo \App\Services\Auth::id() ?></span>
                    </span>
                    <form class="user-menu-item unset border" method="post" action="<?php echo url('auth/logout') ?>">
                        <button class="unset" type="submit">ログアウト</button>
                    </form>
                </div>
                <div class="backdrop" id="user-menu-item-backdrop" role="button" aria-label="閉じる"></div>
            </div>
        <?php endif ?>
    </nav>
    <div class="search-form">
        <form class="search-form-inner" method="GET" action="<?php echo url('search') ?>">
            <label for="q">
            </label>
            <input type="text" id="q" name="q" placeholder="オープンチャットを検索" maxlength="40" autocomplete="off" required>
        </form>
        <div class="backdrop" id="serch_form_backdrop" role="button" aria-label="検索バーを閉じる"></div>
    </div>
</header>
<?php if (\App\Services\Auth::check() === false) : ?>
    <div class="login-modal backdrop" id="login-modal" role="button" aria-label="閉じる">
        <div class="modal_inner" id="modal_inner">
            <button aria-label="閉じる" class="modal_close_btn_area unset" id="login-modal-close-btn">
                <span aria-label="hidden" class="modal_close_btn">×</span>
            </button>
            <header class="modal-login-header unset">
                <div class="modal-site-title">
                    <img src="<?php echo url('assets/icon-192x192.webp') ?>" alt="">
                    <p>オプチャグラフ</p>
                </div>
                <div class="modal-site-desc">オプチャグラフにログインすると、サイト上で便利な機能が利用出来ます。</div>
            </header>
            <form class="unset" method="post" action="<?php echo url('auth/login') ?>">
                <button class="line-login-btn unset" aria-label="LINEでログイン">
                    <img src="<?php echo url('assets/btn_linelogin.webp') ?>" alt="">
                </button>
                <input type="hidden" name="return_to" value="<?php echo $_SERVER['REQUEST_URI'] ?>" />
            </form>
            <div class="modal-login-footer">表示名・IDなどLINEアカウントの情報は取得しません。<br>LINEとのデータ連携は行いません。<br><a class="unset" href="<?php echo url('terms') ?>">利用規約</a>、<a class="unset" href="<?php echo url('privacy') ?>">プライバシーポリシー</a>に同意したうえでログインしてください。</div>
        </div>
    </div>
<?php endif ?>