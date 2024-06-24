<!DOCTYPE html>
<html lang="ja">

<head prefix="og: http://ogp.me/ns#">
    <?php echo gTag(\App\Config\AppConfig::GTM_ID) ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta name="theme-color" content="#800080" />

    <link rel="icon" type="image/png" href="<?php echo fileUrl('assets/study_icon.png') ?>">

    <script defer="defer" src="<?php echo fileUrl($_js) ?>"></script>
    <link rel="stylesheet" href="<?php echo fileUrl($_css) ?>">

    <link rel="canonical" hrefs="<?php echo url('accreditation') ?>">

    <title><?php echo $title ?></title>
    <meta name="description" content="<?php echo $description ?>">
    <meta property="og:locale" content="ja_JP">
    <meta property="og:url" content="<?php echo url('accreditation') ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo $title ?>">
    <meta property="og:description" content="<?php echo $description ?>">
    <meta property="og:site_name" content="オプチャ検定">
    <meta property="og:image" content="<?php echo $ogp ?>">
    <meta name="twitter:card" content="summary">
</head>

<body style="margin: 0;">
    <script type="application/json" id="arg-dto">
        <?php echo json_encode($_argDto, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
    </script>
    <?php if (isset($_argDto_silver)) : ?>
        <script type="application/json" id="arg-dto-silver">
            <?php echo json_encode($_argDto_silver, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
        </script>
    <?php endif ?>
    <?php if (isset($_argDto_gold)) : ?>
        <script type="application/json" id="arg-dto-gold">
            <?php //echo json_encode($_argDto_gold, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); 
            ?>
        </script>
    <?php endif ?>
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="root"></div>
</body>

</html>