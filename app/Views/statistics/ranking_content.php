<!-- 固定ヘッダー -->
<header class="site_header">
    <div class="header_inner">
        <a class="unset header_site_title" href="<?php echo url() ?>">
            <img src="<?php echo url('assets/icon-192x192.webp') ?>" alt="">
            <h1>LINEオープンチャット グラフ</h1>
        </a>
    </div>
</header>
<main class="short-header">
    <!-- 2ページ目以降 -->
    <h2 class="ranking-title">急上昇ランキング</h2>
    <span class="button01label" style="display: inline-block;"><?php echo $pageNumber . ' / ' . $maxPageNumber ?></span>
    <!-- オープンチャット一覧 -->
    <?php statisticsComponent('open_chat_list', compact('openChatList')) ?>
    <!-- 次のページ・前のページボタン -->
    <?php statisticsComponent('pager_nav', compact('pageNumber', 'maxPageNumber') + ['path' => '/ranking']) ?>
</main>