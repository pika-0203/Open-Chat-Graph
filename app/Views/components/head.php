<!-- @param string $_meta -->
<!-- @param array $_css -->
<!-- @param array $noindex -->

<head prefix="og: http://ogp.me/ns#">
    <?php echo gTag(\App\Config\AppConfig::GTAG_ID) ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $_meta ?>
    <link rel="stylesheet" href="<?php echo fileUrl('style/mvp.css') ?>">
    <?php foreach ($_css as $css) : ?>
        <link rel="stylesheet" href="<?php echo fileUrl("style/{$css}.css") ?>">
    <?php endforeach ?>
    <link rel="icon" type="image/png" href="<?php echo url(\App\Config\AppConfig::SITE_ICON_FILE_PATH) ?>">
    <?php if ($noindex ?? false) : ?>
        <meta name="robots" content="noindex,nofollow,noarchive" />
    <?php endif ?>
    <link rel="canonical" hrefs="<?php echo url(strstr(path(), '?', true) ?: path()) ?>">
</head>