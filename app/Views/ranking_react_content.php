<!DOCTYPE html>
<html lang="ja">
<!--
    $_css = array
    $_js = string
    $_meta = Metadata
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
    <script defer="defer" src="<?php echo fileUrl($_js) ?>"></script>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2330982526015125" crossorigin="anonymous"></script>
    <link rel="canonical" hrefs="<?php echo url('ranking') . ($category ? '/' . $category : '') ?>">
    <script async>
        const agentsJsonUrl = 'https://raw.githubusercontent.com/monperrus/crawler-user-agents/master/crawler-user-agents.json'
        async function blockblock() {
            const response = await fetch(agentsJsonUrl)
            const items = await response.json()
            const patterns = items.map(item => item.pattern)
            const REGEX_CRAWLER = patterns.join('|')
            const ua = window.navigator.userAgent
            const result = ua.match(REGEX_CRAWLER)
            if (result !== null) return

            fetch('https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js', {
                    method: 'HEAD',
                    mode: 'no-cors',
                    cache: 'no-store',
                })
                .then()
                .catch((err) => {
                    alert('アドブロックを解除してください')
                })
        }
        blockblock()
    </script>
</head>

<body style="margin: 0">
    <script type="application/json" id="arg-dto">
        <?php echo json_encode($_argDto, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>
    </script>
    <noscript>You need to enable JavaScript to run this app.</noscript>
    <div id="root"></div>
    <?php echo $_breadcrumbsShema ?>
</body>

</html>