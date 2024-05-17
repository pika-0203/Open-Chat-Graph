<!-- @param string $_meta -->
<!-- @param array $_css -->
<!-- @param int $id -->

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
    <script async type="module" crossorigin src="/<?php echo getFilePath('js/chart', 'index-*.js') ?>"></script>
    <script async type="module" crossorigin src="/<?php echo getFilePath('js/comment', 'index-*.js') ?>"></script>
    <link rel="canonical" hrefs="<?php echo url(strstr(path(), '?', true) ?: path()) ?>">
    <?php if (isset($_schema)) : ?>
        <?php echo $_schema ?>
    <?php endif ?>
    <!-- <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2330982526015125" crossorigin="anonymous"></script> -->
</head>