<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('policy_head', compact('_css', '_meta') + ['noindex' => true]) ?>

<body>
    <?php viewComponent('site_header') ?>
    <main>
        <article class="terms">
            <h1 style="letter-spacing: 0px; overflow: hidden;">広告について</h1>
            <p>この広告は行動ターゲティング広告ではないため、クッキーの取得を行いません。</p>
            <p>サイト内のコンテンツに関連する広告を表示しています。</p>
        </article>
        <div style="margin: 1rem auto; max-width: 512px;">
            <?php
            $dto = new \App\Views\Dto\AdsDto;
            $dto->ads_href = url('');
            $dto->ads_img_url = url('assets/ogp.png');
            $dto->ads_paragraph = 'オプチャグラフはユーザーがオープンチャットを見つけて成長傾向をグラフで比較できる場所です。';
            $dto->ads_sponsor_name = 'オプチャグラフ';
            $dto->ads_title = 'オプチャの人数推移をグラフ化';
            $dto->ads_title_button = '詳細を見る';
            $dto->echoAdsElement();
            ?>
        </div>
    </main>
    <footer class="footer-elem-outer">
        <?php viewComponent('footer_inner') ?>
    </footer>
</body>

</html>