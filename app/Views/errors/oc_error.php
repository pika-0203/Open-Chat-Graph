<!DOCTYPE html>
<html lang="ja">

<head prefix="og: http://ogp.me/ns#">
    <?php

    use App\Config\AppConfig;

    echo gTag(\App\Config\AppConfig::GTAG_ID) ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $_meta ?>
    <link rel="stylesheet" href="<?php echo fileUrl('style/mvpmin.css') ?>">
    <?php foreach ($_css as $css) : ?>
        <link rel="stylesheet" href="<?php echo fileUrl("style/{$css}.css") ?>">
    <?php endforeach ?>
    <link rel="icon" type="image/png" href="<?php echo url(\App\Config\AppConfig::SITE_ICON_FILE_PATH) ?>">
<!--     <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2330982526015125" data-overlays="bottom" crossorigin="anonymous"></script>
 --></head>

<body class="body">
    <style>
        * {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

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
            margin-left: 0;
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
        <ins class="adsbygoogle rectangle-ads" style="display:block; background: rgb(250, 250, 250); margin: 0 -1rem;" data-ad-client="ca-pub-2330982526015125" data-ad-slot="8037531176"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        <header style="padding: 1rem 0 0 0; text-align: center">
            <p style="color: #111; font-size: 11px; text-align: left;">「<?php echo $recommend[2] ?>」 ID:<?php echo $open_chat_id ?></p>
            <p style="font-weight: bold; color: #777">このオープンチャットはオプチャグラフから削除されました😇</p>
            <p style="color: #aaa; font-size: 13px">LINE内でルームが削除された可能性があります</p>
        </header>
        <?php if ($recommend[0]) : ?>
            <?php viewComponent('recommend_list2', ['recommend' => $recommend[0], 'member' => 0, 'tag' => $recommend[2], 'id' => 0]) ?>
        <?php endif ?>
        <?php if ($recommend[1]) : ?>
            <ins class="adsbygoogle rectangle-ads" style="display:block; background: rgb(250, 250, 250); margin: 0 -1rem;" data-ad-client="ca-pub-2330982526015125" data-ad-slot="8037531176"></ins>
            <script>
                (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            <?php viewComponent('recommend_list2', ['recommend' => $recommend[1], 'member' => 0, 'tag' => $recommend[2], 'id' => 0]) ?>
        <?php endif ?>
        <div class="top-list" style="all: unset; display: all;">
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=all&order=desc&sort=member') ?>">
                <span class="ranking-readMore">カテゴリーからオープンチャットを探す</span>
            </a>
        </div>
        <ins class="adsbygoogle rectangle-ads" style="display:block; background: rgb(250, 250, 250); margin: 0 -1rem;" data-ad-client="ca-pub-2330982526015125" data-ad-slot="8037531176"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        <p style="width: fit-content; margin: 1rem auto;"><a style="color: #777; font-size: 11px;" href="<?php echo AppConfig::LINE_OPEN_URL . $_deleted['emid'] . AppConfig::LINE_OPEN_URL_SUFFIX; ?>">オープンチャットのURL（LINEオープンチャット公式サイト）</a></p>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
</body>

</html>