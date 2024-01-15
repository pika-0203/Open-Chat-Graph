<!DOCTYPE html>
<html lang="ja">
<!--
    $_css = array
    $_js = string
    $_meta = Metadata
    $_jsonData = string
    $rankingUpdatedAt = string
-->

<head prefix="og: http://ogp.me/ns#">
    <?php echo gTag(\App\Config\AppConfig::GTAG_ID) ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <?php echo $_meta ?>
    <link rel="icon" type="image/png" href="<?php echo url(\App\Config\AppConfig::SITE_ICON_FILE_PATH) ?>">
    <?php foreach ($_css as $css) : ?>
        <link rel="stylesheet" href="<?php echo fileUrl($css) ?>">
    <?php endforeach ?>

    <script>
        window.subCategories = <?php echo $_jsonData; ?>;
        window.rankingUpdatedAt = "<?php echo convertDatetime($rankingUpdatedAt, true); ?>";
    </script>
    <script defer="defer" src="<?php echo fileUrl($_js) ?>"></script>
</head>

<body style="margin: 0">
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="root"></div>
</body>

</html>