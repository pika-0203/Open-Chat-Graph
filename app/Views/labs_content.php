<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('policy_head', compact('_css', '_meta')) ?>

<body>
    <div class="body">
        <?php viewComponent('site_header') ?>
        <main>
            <article class="terms">
                <h1 style="letter-spacing: 0px;">分析Labsの試験機能</h1>
                <p>オプチャグラフの新しい分析機能をお試しいただけます。開発初期段階にある試験運用版でテストを行い、有用な機能は本採用します。分析機能テストに関するフィードバックは<a href="<?php echo url('policy#comments') ?>">コメント欄</a>にお願いいたします。</p>
                <h2>タグで見るトレンド動向</h2>
                <img src="/labs-img/tags.webp" />
                <p>タグによる分類を用いたトレンド分析では、単純ながらも重要な増減数や合計人数の集計を行います。これにより、各トピックの人気度やその変動を捉えることができます。</p>
                <a class="top-ranking-readMore unset" style="margin:0" href="<?php echo url('labs/tags') ?>">
                    <span class="ranking-readMore">タグで見るトレンド動向に移動<span class="small"></span>
                </a>
            </article>
        </main>
        <footer>
            <?php viewComponent('footer_inner') ?>
        </footer>
    </div>
    <?php echo $_breadcrumbsShema ?>
</body>

</html>