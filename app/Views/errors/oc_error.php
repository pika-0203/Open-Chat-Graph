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
    </style>

    <!-- 固定ヘッダー -->
    <main class="main">
        <div style="margin: 0 -1rem; ">
            <?php viewComponent('site_header') ?>
        </div>
        <header>
            <p style="font-weight: bold;">このオープンチャットは削除されました😇</p>
        </header>
    </main>
    <?php /** @var \App\Controllers\Pages\NotFoundPageController $c */
    try {
        $c = app(\App\Controllers\Pages\NotFoundPageController::class);
        $c->index($recommend)->render();
    } catch (\Throwable $e) {
        echo 'データ取得エラー';
    }
    ?>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
</body>

</html>