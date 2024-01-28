<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta') + ['noindex' => true]) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <main class="ranking-page-main">
        <article>
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date" style="gap: 4px; flex-direction: column;">
                    <h2 class="unset">
                        <span class="openchat-list-title" style="margin: 0;">最近のランクイン</span>
                    </h2>
                    <small style="font-size: 12px; color:#000">公式ランキングにランクインしたオプチャを新しい順で表示</small>
                    <small style="font-size: 12px;">LINE公式サイトにて掲載が終了したオープンチャットのデータは、自動的にオプチャグラフから削除されます。</small>
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