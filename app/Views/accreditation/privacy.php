<?php

use App\Config\AppConfig;

?>

<!DOCTYPE html>
<html lang="ja">

<head prefix="og: http://ogp.me/ns#">
    <?php echo gTag(AppConfig::GTM_ID) ?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プライバシーポリシー</title>
    <meta name="description" content="プライバシーポリシー">
    <meta property="og:locale" content="ja_JP">
    <meta property="og:url" content="<?php url(path()) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="プライバシーポリシー">
    <meta property="og:description" content="プライバシーポリシー">
    <meta property="og:site_name" content="オプチャ検定">
    <meta name="twitter:card" content="summary">
    <link rel="icon" type="image/png" href="<?php echo fileUrl('assets/study_icon.png') ?>">
    <link rel="stylesheet" href="<?php echo fileUrl('style/mvp.css') ?>">
</head>

<body>
    <style>
        hr {
            border-bottom: 1px #efefef solid;
            margin: 2rem 0;
        }
    </style>
    <main style="max-width: 600px;">
        <article>
            <h1>プライバシーポリシー</h1>
            <hr>    
            <h2>個人情報の利用目的</h2>
            <p>当サイトでは、メールでのお問い合わせの際に、名前（ニックネーム）、連絡先等の個人情報を送信いただく場合がございます。</p>
            <p>これらの個人情報は質問に対する回答や必要な情報を電子メールなどでご連絡する場合に利用させていただくものであり、個人情報をご提供いただく際の目的以外では利用いたしません。</p>
            <hr>
            <h2>アクセス解析ツールについて</h2>
            <p>当サイトでは、Googleによるアクセス解析ツール「Googleアナリティクス」を利用しています。</p>
            <p>このGoogleアナリティクスはトラフィックデータの収集のためにCookieを使用しています。このトラフィックデータは匿名で収集されており、個人を特定するものではありません。この機能はCookieを無効にすることで収集を拒否することが出来ますので、お使いのブラウザの設定をご確認ください。この規約に関しての詳細は「<a href="https://marketingplatform.google.com/about/analytics/terms/jp/" target="_blank">Googleアナリティクスサービス利用規約</a>」や「<a href="https://policies.google.com/technologies/ads?hl=ja" target="_blank">Googleポリシーと規約ページ</a>」をご確認ください。</p>
            <hr>
            <h2>免責事項</h2>
            <p>当サイトからリンクやバナーなどによって他のサイトに移動された場合、移動先サイトで提供される情報、サービス等について一切の責任を負いません。</p>
            <p>当サイトのコンテンツ・情報につきまして、可能な限り正確な情報を掲載するよう努めておりますが、誤情報が入り込んだり、情報が古くなっていることもございます。</p>
            <p>当サイトに掲載された内容によって生じた損害等の一切の責任を負いかねますのでご了承ください。</p>
            <hr>
            <h2>著作権について</h2>
            <p>当サイトで掲載している画像の著作権・肖像権等は各権利所有者に帰属致します。権利を侵害する目的ではございません。ページの内容や掲載画像等に問題がございましたら、各権利所有者様本人が<a href="mailto:support@openchat-review.me">お問い合わせ窓口のメール</a>でご連絡ください。できるだけ迅速に対応させて頂きます。</p>
            <hr>
            <h2>プライバシーポリシーの変更について</h2>
            <p>当サイトは、個人情報に関して適用される日本の法令を遵守するとともに、本ポリシーの内容を適宜見直しその改善に努めます。</p>
            <p>修正された最新のプライバシーポリシーは常に本ページにて開示されます。</p>
            <hr>
            <aside>
                <h3>メールでのお問い合わせ先</h3>
                <p>
                    <a href="mailto:support@openchat-review.me">support@openchat-review.me</a>
                </p>
            </aside>
        </article>
    </main>
</body>

</html>