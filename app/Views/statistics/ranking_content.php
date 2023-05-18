<!DOCTYPE html>
<html lang="ja">
<?php statisticsComponent('head', compact('_css', '_meta')) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php statisticsComponent('site_header') ?>
    <main>
        <p class="openchat-list-title unset">急上昇ランキング<small style="font-weight: normal; margin-left: 0.5rem;">(過去１週間)</small></p>
        <span class="button01label" style="display: inline-block;"><?php echo $pageNumber . ' / ' . $maxPageNumber ?></span>
        <!-- オープンチャット一覧 -->
        <?php statisticsComponent('open_chat_list', compact('openChatList')) ?>
        <!-- 次のページ・前のページボタン -->
        <?php statisticsComponent('pager_nav', compact('pageNumber', 'maxPageNumber') + ['path' => '/ranking']) ?>
    </main>
    <?php statisticsComponent('footer') ?>
    <script defer src="/js/site_header_footer_3.js"></script>
</body>

</html>