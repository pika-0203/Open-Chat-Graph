<?php $show = [];

function memberCount(int $count)
{
?>
    <span style="color:<?php echo $count === 0 ? '#aaa' : ($count > 0 ? '#4d73ff' : '#ff5d6d') ?>">
        <?php if ($count === 0) : ?>
            ±0
        <?php else : ?>
            <?php echo signedNumF($count) ?>
        <?php endif ?>
    </span>
<?php
}

?>

<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta', '_schema')) ?>

<body class="body">
    <?php viewComponent('site_header', compact('_updatedAt')) ?>
    <main style="margin-bottom: 0;">
        <p style="font-size: 13px; color: #555">各タグを、最も近いカテゴリに分類して表示しています。タグ内のルーム自体は、様々なカテゴリに属しています。</p>
        <p style="font-size: 13px; color: #555">タグボタン内の件数はルームのトータル数、24H・1Wは全ルームの合計人数増減です。</p>
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
                                    <div>
                                        <div style="line-height: 1.3;"><?php echo \App\Services\Recommend\RecommendUtility::extractTag($tag['tag']) ?></div>
                                        <small style="display:block; font-weight:normal;line-height: 1.2;"><?php echo number_format($tag['record_count']) ?>件</small>
                                    </div>
                                    <div>
                                        <small style="color:#aaa; display:block; margin-left:4px;line-height: 1.2;">24H</small>
                                        <small style="color:#aaa; display:block; margin-left:4px;line-height: 1.2;">1W</small>
                                    </div>
                                    <div>
                                        <small style="display:block; margin-left:4px;line-height: 1.2;"><?php memberCount($tag['hour24'] ?? 0) ?></small>
                                        <small style="display:block; margin-left:4px;line-height: 1.2;"><?php memberCount($tag['week'] ?? 0) ?></small>
                                    </div>
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