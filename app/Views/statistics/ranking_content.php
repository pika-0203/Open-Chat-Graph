<!DOCTYPE html>
<html lang="ja">
<?php statisticsComponent('head', compact('_css', '_meta')) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php statisticsComponent('site_header') ?>
    <main class="ranking-page-main">
        <article>
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date">
                    <h2 class="unset">
                        <span class="openchat-list-title">ランキング</span>
                    </h2>
                    <div class="refresh-time">
                        <div class="refresh-icon"></div>
                        <time datetime="<?php echo dateTimeAttr($updatedAt) ?>"><?php echo getDailyRankingDateTime($updatedAt) ?></time>
                    </div>
                </div>
            </header>
            <nav class="chart-btn-nav" id="chart-btn-nav">
                <button class="chart-btn unset" id="btn-daily" <?php $_isDisabledBtn('daily') ?>>
                    <div class="btn-text">
                        <span>前日比</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
                <button class="chart-btn unset" id="btn-weekly" <?php $_isDisabledBtn('weekly') ?>>
                    <div class="btn-text">
                        <span>前週比</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
            </nav>
            <!-- select要素ページネーション -->
            <nav class="page-select unset">
                <form class="unset">
                    <select id="page-selector" class="unset">
                        <?php echo $_select ?>
                    </select>
                    <label for="page-selector" class="unset"><span><?php echo $_label ?></span></label>
                </form>
            </nav>
            <?php statisticsComponent('open_chat_list', compact('openChatList', 'isDaily')) ?>
            <!-- 次のページ・前のページボタン -->
            <?php statisticsComponent('pager_nav', compact('pageNumber', 'maxPageNumber', '_queryString') + ['path' => '/ranking']) ?>
        </article>
    </main>
    <footer>
        <?php statisticsComponent('footer_inner') ?>
    </footer>
    <script defer src="/js/site_header_footer_5.js"></script>
    <script>
        ((el) => {
            if (!el) return

            el.addEventListener('change', () => {
                el.value && (location.href = el.value)
            })
        })(document.getElementById('page-selector'));
    </script>
    <script>
        const btnDaily = document.getElementById('btn-daily')
        const btnWeekly = document.getElementById('btn-weekly')
        const rankingUrl = '<?php echo url('ranking') ?>';

        btnDaily.addEventListener('click', e => {
            btnDaily.disabled = true
            btnWeekly.disabled = false
            location.href = rankingUrl
        });

        btnWeekly.addEventListener('click', e => {
            btnDaily.disabled = false
            btnWeekly.disabled = true
            location.href = (rankingUrl + '?l=w')
        });
    </script>
    <?php echo $_schema ?>
</body>

</html>