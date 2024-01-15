<!DOCTYPE html>
<html lang="jp">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo fileUrl("style/mvp.css") ?>">
    <link rel="stylesheet" href="<?php echo fileurl("style/site_header.css") ?>">
    <link rel="stylesheet" href="<?php echo fileurl("style/site_footer.css") ?>">
    <title><?php echo $title ?? 'admin' ?></title>
</head>

<body>
    <?php viewComponent('site_header') ?>
    <main>
        <p><?php echo nl2br($message) ?? '' ?></p>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
</body>

</html>