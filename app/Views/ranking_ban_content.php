<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta')) ?>

<body class="body">
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

        .p-small {
            font-size: 13px;
            color: #777;

        }
    </style>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <main class="ranking-page-main" style="margin-top: 8px; padding-top: 0;">
        <article>
            <header class="openchat-list-title-area unset">
                <div style="flex-direction: column;">
                    <h2 class="list-title">
                        オプチャグラフに最近登録されたオープンチャット
                    </h2>
                    <p>
                        <small class="p-small">このページではオプチャグラフに登録されたオープンチャットを登録日時順で表示します。</small>
                    </p>
                    <p>
                        <small class="p-small">LINE公式サイトのランキングは1時間毎に更新されており、オプチャグラフもその時間帯に合わせて公式サイトからデータを取得しています。</small>
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
            <?php viewComponent('open_chat_list', compact('openChatList', 'isAdmin')) ?>
            <!-- 次のページ・前のページボタン -->
            <?php viewComponent('pager_nav', compact('pageNumber', 'maxPageNumber') + ['path' => $path]) ?>
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
    <?php echo $_breadcrumbsShema ?>
</body>

</html>