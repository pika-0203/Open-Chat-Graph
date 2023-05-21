<!DOCTYPE html>
<html lang="ja">
<?php statisticsComponent('head', compact('_css', '_meta')) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php statisticsComponent('site_header') ?>
    <main>
        <article>
            <header class="openchat-list-title-area unset">
                <h2 class="unset">
                    <span class="openchat-list-title">参加人数の急上昇ランキング</span>
                    <span class="openchat-list-subtitle">(毎日更新)</span>
                </h2>
                <div class="openchat-list-date">
                    <div class="refresh-icon"></div>
                    <time datetime="<?php echo dateTimeAttr($updatedAt) ?>"><?php echo getDailyRankingDateTime($updatedAt) ?></time>
                </div>
            </header>
            <!-- select要素ページネーション -->
            <nav class="page-select unset">
                <form class="unset">
                    <select id="page-selector" class="unset">
                        <?php echo $_select ?>
                    </select>
                    <label for="page-selector" class="unset"><span><?php echo $_label ?></span></label>
                </form>
            </nav>
            <?php statisticsComponent('open_chat_list', compact('openChatList')) ?>
            <!-- 次のページ・前のページボタン -->
            <?php statisticsComponent('pager_nav', compact('pageNumber', 'maxPageNumber') + ['path' => '/ranking']) ?>
        </article>
    </main>
    <?php statisticsComponent('footer') ?>
    <script defer src="/js/site_header_footer_4.js"></script>
    <script>
        ((el) => {
            if (!el) return

            el.addEventListener('change', () => {
                el.value && (location.href = el.value)
            })
        })(document.getElementById('page-selector'));
    </script>
</body>

</html>