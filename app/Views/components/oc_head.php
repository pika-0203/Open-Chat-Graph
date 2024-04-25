<!-- @param string $_meta -->
<!-- @param array $_css -->
<!-- @param int $id -->

<head prefix="og: http://ogp.me/ns#">
    <?php echo gTag(\App\Config\AppConfig::GTAG_ID) ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="google-adsense-account" content="ca-pub-2330982526015125">
    <?php echo $_meta ?>
    <link rel="stylesheet" href="<?php echo fileUrl('style/mvpmin.css') ?>">
    <?php foreach ($_css as $css) : ?>
        <link rel="stylesheet" href="<?php echo fileUrl("style/{$css}.css") ?>">
    <?php endforeach ?>
    <link rel="icon" type="image/png" href="<?php echo url(\App\Config\AppConfig::SITE_ICON_FILE_PATH) ?>">
    <script type="module" crossorigin src="/<?php echo getFilePath('js/chart', 'index-*.js') ?>"></script>
    <script type="module" crossorigin src="/<?php echo getFilePath('js/comment', 'index-*.js') ?>"></script>
    <link rel="canonical" hrefs="<?php echo url(strstr(path(), '?', true) ?: path()) ?>">
    <?php if (isset($_schema)) : ?>
        <?php echo $_schema ?>
    <?php endif ?>
</head>