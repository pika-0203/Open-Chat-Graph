<?php $show = []; ?>
<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta', '_schema') + ['noindex' => true]) ?>

<body class="body">
    <?php viewComponent('site_header', compact('_updatedAt')) ?>
    <main style="margin-bottom: 0;">
        <p style="font-size: 13px; color: #555">各タグを、最も近いカテゴリに分類して表示しています。タグ内のルーム自体は、様々なカテゴリに属しています。</p>
        <aside class="list-aside ranking-desc">
            <?php viewComponent('recommend_tag_desc') ?>
        </aside>
        <?php foreach ($categories as $key => $category) : ?>
            <article class="top-ranking" style="padding-top: 0; margin-top: 0; margin-bottom: 1rem">
                <div>
                    <header class="openchat-list-title-area unset">
                        <div class="openchat-list-date unset ranking-url">
                            <h2 class="unset">
                                <span class="openchat-list-title"><?php echo $key ? $category : 'その他' ?></span>
                            </h2>
                        </div>
                    </header>
                    <ul class="tag-list open">
                        <?php foreach ($tagsGroup[$key] as $tag) : ?>
                            <?php
                            if (!$tag['tag'] || in_array($tag, $show)) continue;
                            $show[] = $tag
                            ?>
                            <li>
                                <a class="tag-btn" href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($tag['tag']))) ?>">
                                    <span><?php echo \App\Services\Recommend\RecommendUtility::extractTag($tag['tag']) ?></span>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </article>
        <?php endforeach ?>
    </main>
    <footer>
        <?php viewComponent('footer_share_nav', ['title' => $_meta->title]) ?>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <?php echo $_meta->generateTopPageSchema() ?>
</body>

</html>