<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta') + ['noindex' => true]) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <main class="ranking-page-main">
        <div style="width: 100%;">
            <form class="search-form2 search-form-inner" id="id_searchForm" method="GET" action="<?php echo url('search') ?>">
                <label class="search-label2" for="q-page">
                </label>
                <input type="text" id="q-page" name="q" placeholder="オープンチャットを検索" maxlength="40" autocomplete="off" value="<?php echo $q ?>" required>
            </form>
        </div>
        <header class="openchat-list-title-result-area unset" style="display: inline-block;">
            <h2 class="openchat-list-title-result">「<?php echo $q ?>」の検索結果</h2>
        </header>
        <?php if (isset($openChatList)) : ?>
            <!-- 検索結果が見つかった場合 -->
            <?php if ($maxPageNumber === 1) : ?>
                <!-- 検索結果が単ページの場合 -->
                <span class="button01label" style="display: inline-block;"><?php echo $count ?> 件</span>
                <!-- オープンチャット一覧 -->
                <?php viewComponent('open_chat_list', compact('openChatList')) ?>
            <?php else : ?>
                <!-- 検索結果が複数ページの場合 -->
                <?php if ($pageNumber === 1) : ?>
                    <!-- 1ページ目の場合 -->
                    <span class="button01label" style="display: inline-block;"><?php echo "{$count} 件" ?></span>
                <?php else : ?>
                    <!-- 2ページ目以降の場合 -->
                    <span class="button01label" style="display: inline-block;"><?php echo "{$count} 件中 {$pageNumber} ページ目" ?></span>
                <?php endif ?>
                <!-- オープンチャット一覧 -->
                <?php viewComponent('open_chat_list', compact('openChatList')) ?>
                <!-- 次のページ・前のページボタン -->
                <?php viewComponent('search_pager_nav', compact('pageNumber', 'maxPageNumber', 'q')) ?>
            <?php endif ?>
        <?php else : ?>
            <!-- 検索結果が0件の場合 -->
            <span class="button01label" style="display: inline-block;">0 件</span>
        <?php endif ?>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
</body>

</html>