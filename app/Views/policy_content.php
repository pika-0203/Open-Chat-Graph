<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta') + ['noindex' => true]) ?>

<body>
    <?php viewComponent('site_header') ?>
    <main>
        <h1 style="letter-spacing: 0px;">ポリシー</h1>
        <article class="terms">
            <p>当サイトは<a href="https://openchat.line.me/jp" rel="external" target="_blank">LINEオープンチャット公式サイト</a><span class="line-link-icon"></span>で送信可能化されたデータを集めて、集計・加工を施して図やグラフ等に視覚化することで新たな知見を得ることができます。</p>
            <h2>サイトの目的</h2>
            <p>・オープンチャットの管理者が成長傾向を把握し、比較できる事で運営に役立つ</p>
            <p>・ユーザーがオープンチャットを見つけて参加する機会を作る</p>
            <h2>コンテンツの権利</h2>
            <p>データの収集は<a href="https://openchat.line.me/robots.txt" rel="external" target="_blank">LINEオープンチャット公式サイトのrobots.txt</a><span class="line-link-icon"></span>を尊重して行います。</p>
            <p>公式サイトにて掲載が終了したオープンチャットは、当サイトから削除されます。</p>
            <p>当サイトにおける投稿コンテンツに対して有する権利はオープンチャットの作成者及びLINE株式会社が従前どおり保持し、当サイトがかかる権利を取得することはありません。</p>
            <p>LINEの詳細は<a href="https://terms.line.me/line_terms?lang=ja" rel="noopener noreferrer" target="_blank">LINE利用規約</a>や<a href="https://terms.line.me/line_Square_TOU_JP?lang=ja" rel="noopener noreferrer" target="_blank">LINEオープンチャット利用規約</a>をご覧ください。</p>
            <p>オープンチャットが当サイトに登録されないようにするには、オープンチャットの説明文に #nolog を含めます。</p>
            <p>既に登録済みのオープンチャットは、データ更新時に #nolog を検出すると削除されます。</p>
            <h2>Cookie</h2>
            <p>Googleアナリティクスのデータ収集を行うためCookieを使用しています。<br>このデータは匿名で収集されており、個人を特定するものではありません。</p>
            <p>詳細は<a href="https://marketingplatform.google.com/about/analytics/terms/jp/" rel="noopener noreferrer" target="_blank">Googleアナリティクスサービス利用規約</a>や<a href="https://policies.google.com/technologies/ads?hl=ja" rel="noopener noreferrer" target="_blank">Googleポリシーと規約</a>をご覧ください。</p>
            <p>Cookieを無効にすることで収集を拒否することが出来ます。</p>
            <h2>お問い合わせ窓口</h2>
            <p>E-mail: <a href="mailto:support@openchat-review.me">support@openchat-review.me</a></p>
        </article>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
</body>

</html>