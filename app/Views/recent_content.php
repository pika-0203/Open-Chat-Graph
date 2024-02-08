<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta') + ['noindex' => true]) ?>

<body>
    <style>
        hr {
            border-bottom: solid 1px var(--border-color);
            margin: 12px 0;
        }

        .list-title {
            color: #111;
            all: unset;
            font-size: 20px;
            font-weight: bold;
        }

        .page-select {
            margin-top: 1.75rem;
            padding-bottom: 0.85rem;
        }
    </style>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <main class="ranking-page-main">
        <article>
            <header class="openchat-list-title-area unset">
                <div style="flex-direction: column;">
                    <h2>
                        <span class="list-title">最近登録されたオープンチャット</span>
                    </h2>
                    <p>
                        <small style="font-size: 12px; color:#000">「公式ランキングにランクインした順」で表示します</small>
                    </p>
                </div>
            </header>
            <!-- select要素ページネーション -->
            <hr>
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
    </script>
    <?php echo $_schema ?>
</body>

</html>