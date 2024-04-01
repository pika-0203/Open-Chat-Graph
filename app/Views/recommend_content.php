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

        .ranking-page-main {
            padding-top: 0;
        }

        .openchat-list-title-area h2 {
            margin: 0;
        }

        .ranking-page-main .openchat-list-title-area p {
            margin: 0;
        }

        @media screen and (max-width: 512px) {
            .list-title {
                font-size: 17.5px;
            }
        }
    </style>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <main class="ranking-page-main">
        <article>
            <header class="openchat-list-title-area unset">
                <div style="flex-direction: column;">
                    <h2>
                        <span class="list-title">【最新】「なりきり」関連のおすすめ人気オープンチャット50件</span>
                    </h2>
                    <p>
                        <small style="font-size: 13px; color:#777">公式ランキングにランクインされた順で表示</small>
                    </p>
                </div>
            </header>
            <hr>
            <?php viewComponent('open_chat_list', compact('openChatList')) ?>
        </article>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>

    <?php echo $_breadcrumbsShema ?>
</body>

</html>