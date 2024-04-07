<!DOCTYPE html>
<html lang="ja">

<head prefix="og: http://ogp.me/ns#">
    <?php echo gTag(\App\Config\AppConfig::GTAG_ID) ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $_meta ?>
    <link rel="stylesheet" href="<?php echo fileUrl('style/mvpmin.css') ?>">
    <?php foreach ($_css as $css) : ?>
        <link rel="stylesheet" href="<?php echo fileUrl("style/{$css}.css") ?>">
    <?php endforeach ?>
    <link rel="icon" type="image/png" href="<?php echo url(\App\Config\AppConfig::SITE_ICON_FILE_PATH) ?>">
</head>

<body class="body">
    <style>
        /* Increase size of the main heading */
        h1 {
            font-size: 5rem;
        }

        /* Break long lines in the code section */
        code {
            word-wrap: break-word;
        }

        /* Set width, center, and add padding to the ordered list */
        ol {
            width: fit-content;
            margin: 0 auto;
            margin-top: 1.5rem;
            padding: 0 1rem;
        }

        /* Break URLs to fit in the list */
        a {
            word-break: break-all;
        }

        .main {
            max-width: var(--width);
        }

        @media screen and (min-width: 512px) {
            .main .recommend-list li:nth-child(n + 5) {
                display: block;
            }

            .main .recommend-list li:nth-child(n + 9) {
                display: none;
            }

            .main .recommend-list.show-all li:nth-child(n + 9) {
                display: block;
            }
        }
    </style>

    <!-- 固定ヘッダー -->
    <main class="main">
        <div style="margin: 0 -1rem; ">
            <?php viewComponent('site_header') ?>
        </div>
        <header style="padding: 6rem 0; text-align: center">
            <p style="font-weight: bold; color: #777">このオープンチャットは削除されました😇</p>
            <p style="color: #aaa; font-size: 12px">LINEオープンチャット内でトークルームが削除されました</p>
        </header>
        <?php if ($recommend[0]) : ?>
            <?php viewComponent('recommend_list', ['recommend' => $recommend[0], 'member' => 0, 'tag' => $recommend[2]]) ?>
            <hr style="margin: 1rem 0;">
        <?php endif ?>
        <?php if ($recommend[1]) : ?>
            <?php viewComponent('recommend_list', ['recommend' => $recommend[1], 'member' => 0, 'tag' => $recommend[2]]) ?>
        <?php endif ?>
        <hr style="margin: .5rem 0;">
        <article class="top-list">
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=all&order=desc&sort=member') ?>">
                <span class="ranking-readMore">カテゴリーからオープンチャットを探す</span>
            </a>
            <hr style="margin: .5rem 0;">
        </article>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
</body>

</html>