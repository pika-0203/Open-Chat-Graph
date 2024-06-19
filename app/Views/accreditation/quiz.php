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

    <title>公認メンター監修・オプチャ検定｜練習問題</title>
    <meta name="description" content="オプチャ検定の練習問題に挑戦しよう！">
    <meta property="og:locale" content="ja_JP">
    <meta property="og:url" content="<?php url('accreditation') ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="公認メンター監修・オプチャ検定｜練習問題">
    <meta property="og:description" content="オプチャ検定の練習問題に挑戦しよう！">
    <meta property="og:site_name" content="オプチャ検定">
    <meta property="og:image" content="<?php echo fileUrl('assets/quiz-ogp.png') ?>">
    <meta name="twitter:card" content="summary">
</head>

<body style="margin: 0;">
    <script type="application/json" id="arg-dto">
        <?php echo json_encode($_argDto, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
    </script>
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="root"></div>
</body>

</html>