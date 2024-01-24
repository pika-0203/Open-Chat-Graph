<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta') + ['noindex' => true]) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <main class="ranking-page-main">
        <article>
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date">
                    <h2 class="unset">
                        <span class="openchat-list-title">最近の登録・更新</span>
                    </h2>
                </div>
            </header>
            <nav class="list-btn-nav" id="list-btn-nav">
                <button class="list-btn unset" id="recent" <?php if ($path === 'recent') echo 'disabled' ?>>
                    <div class="btn-text">
                        <span>登録</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
                <button class="list-btn unset" id="changes" <?php if ($path === 'recent/changes') echo 'disabled' ?>>
                    <div class="btn-text">
                        <span>変更履歴</span>
                    </div>
                    <div class="btn-buttom"></div>
                </button>
                <button class="list-btn unset" id="deleted" <?php if ($path === 'recent/deleted') echo 'disabled' ?>>
                    <div class="btn-text">
                        <span>削除</span>
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
            <?php viewComponent('open_chat_list', compact('openChatList')) ?>
            <!-- 次のページ・前のページボタン -->
            <?php viewComponent('pager_nav', compact('pageNumber', 'maxPageNumber') + ['path' => '/' . $path]) ?>
        </article>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <script>
        ;
        (function(el) {
            if (!el) return

            el.addEventListener('change', () => {
                el.value && (location.href = el.value)
            })
        })(document.getElementById('page-selector'))

        ;
        (function() {
            const pageMove = (path = '') => location.href = path
            const buttons = document.getElementById('list-btn-nav').querySelectorAll('.list-btn')

            buttons.forEach(el => el.addEventListener('click', e => {
                if (e.target.closest("#recent")) {
                    pageMove('/recent')
                } else if (e.target.closest("#changes")) {
                    pageMove('/recent/changes')
                } else if (e.target.closest("#deleted")) {
                    pageMove('/recent/deleted')
                }
            }))
        })()
    </script>
    <?php echo $_schema ?>
</body>

</html>