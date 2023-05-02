<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo $_meta ?>
    <link rel="stylesheet" href="<?php echo url("assets/mvp.css") ?>">
    <?php foreach ($_css ?? [] as $css) : ?>
        <link rel="stylesheet" href="<?php echo url("assets/{$css}.css") ?>">
    <?php endforeach ?>
    <link rel="apple-touch-icon" type="image/png" href="<?php echo url('assets/apple-touch-icon-180x180.png') ?>">
    <link rel="icon" type="image/png" href="<?php echo url('assets/icon-192x192.png') ?>">
</head>

<body>