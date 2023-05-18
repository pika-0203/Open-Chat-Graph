<!-- @param string $_meta -->
<!-- @param array $_css -->

<head prefix="og: http://ogp.me/ns#">
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-DBS3CW3XH5"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-DBS3CW3XH5');
    </script>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $_meta ?>
    <link rel="stylesheet" href="<?php echo url("assets/mvp_5.css") ?>">
    <?php foreach ($_css as $css) : ?>
        <link rel="stylesheet" href="<?php echo url("assets/{$css}.css") ?>">
    <?php endforeach ?>
    <link rel="apple-touch-icon" type="image/png" href="<?php echo url('assets/apple-touch-icon-180x180.png') ?>">
    <link rel="icon" type="image/png" href="<?php echo url('assets/icon-192x192.png') ?>">
</head>