<!DOCTYPE html>
<html lang="ja">
<?php

use Shadow\Kernel\Reception as R;

viewComponent('head', compact('_css', '_meta')) ?>

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
    <?php viewComponent('site_header', compact('_updatedAt')) ?>
    <main class="ranking-page-main" style="margin-top: 8px; padding-top: 0;">
        <article>
            <header class="openchat-list-title-area unset">
                <div style="flex-direction: column;">
                    <h2 class="list-title">
                        ランキング掲載分析
                    </h2>
                    <p>
                        <small class="p-small">ランキング掲載・未掲載の履歴をその時の状況と共に一覧表示します。</small>
                    </p>
                </div>
            </header>
            <form>
                <label for="pet-select">ルーム内容の変更:</label>
                <select id="pet-select" name="change">
                    <option value="0" <?php if (R::input('change') == 0) echo 'selected' ?>>あり</option>
                    <option value="2" <?php if (R::input('change') == 2) echo 'selected' ?>>両方</option>
                    <option value="1" <?php if (R::input('change') == 1) echo 'selected' ?>>なし</option>
                </select>
                <input type="submit">
            </form>
            <!-- select要素ページネーション -->
            <hr>
            <?php viewComponent('open_chat_list_ranking_ban', compact('openChatList', '_now')) ?>
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
</body>

</html>