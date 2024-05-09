<?php

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
        <div style="position: absolute; top: -3.5rem;" aria-hidden="true" id="top"></div>
        <p style="font-size: 13px; color: #777">各タグを、近そうなカテゴリに分類して表示しています。タグ内のルームは様々なカテゴリに属しています。</p>
        <p style="font-size: 13px; color: #777">タグを探すときは、ブラウザの機能でページ内のテキストを検索してください。</p>
        <aside class="list-aside ranking-desc" style="margin: 1rem 0;">
            <details class="icon-desc">
                <summary style="font-size: 14px;">タグ内の人数集計について</summary>
                <p class="recommend-desc">
                    タグ内の合計は、全ルームの合計人数です。<br>1H（1時間）、24H（24時間）、1W（1週間）は各期間における全ルームの合計人数増減です。
                </p>
                <p class="recommend-desc">
                    集計は1時間ごとに更新されます。（合計人数、1H、24H）<br>1Wのみ1日ごと（0:00頃）の更新です。
                </p>
                <p class="recommend-desc">
                    集計の対象は、公式ランキングに掲載中のルームのみです。<br>（人数が10人未満、または1週間以上人数の変動が無いルームは対象外）
                </p>
            </details>
        </aside>
        <?php viewComponent('recommend_tag_desc') ?>
        <article class="top-ranking" style="padding:1rem 0; margin-top: 1rem; margin-bottom: 1rem; position: relative;">
            <div>
                <header class="openchat-list-title-area unset">
                    <div class="openchat-list-date unset ranking-url">
                        <h2 class="unset">
                            <span class="openchat-list-title">カテゴリー</span>
                        </h2>
                        <span style="font-weight: normal; color:#aaa; font-size:13px; margin: 0">各カテゴリまで移動</span>
                    </div>
                </header>
                <div style="margin: 1rem; margin-bottom: 0; margin-top: .5rem;">
                    <?php foreach ($categories as $key => $category) : ?>
                        <a style="font-size:15px; text-wrap:nowrap; margin-bottom:20px; margin-right: 1px; display:inline-flex; gap:2px; text-decoration:none;" href="#<?php echo $key ?>">
                            <span style="color:#111; text-decoration:underline; font-weight: bold;"><?php echo $key ? $category : 'その他' ?></span>
                            <span style="color:#777; font-size:10px; margin: 0; line-height: 1.5; font-weight: bold;"><?php echo count($tagsGroup[$key]) ?>タグ</span>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        </article>
        <?php foreach ($categories as $key => $category) : ?>
            <article class="top-ranking" style="padding-top: 0; margin-top: 0; margin-bottom: 1rem; position: relative;">
                <div style="position: absolute; top: -3.5rem;" id="<?php echo $key ?>" aria-hidden="true"></div>
                <div>
                    <header class="openchat-list-title-area unset">
                        <div class="openchat-list-date unset ranking-url">
                            <h2 class="unset">
                                <span class="openchat-list-title"><?php echo $key ? $category : 'その他' ?></span>
                            </h2>
                            <span style="font-weight: normal; color:#aaa; font-size:13px; margin: 0"><?php echo count($tagsGroup[$key]) ?>個のタグ</span>
                        </div>
                    </header>
                    <ul class="tag-list open">
                        <?php foreach ($tagsGroup[$key] as $tag) : ?>
                            <li>
                                <a class="tag-btn" style="height: unset; padding: 4px 14px;" href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($tag['tag']))) ?>">
                                    <div>
                                        <div style="line-height: 1.3;"><?php echo \App\Services\Recommend\RecommendUtility::extractTag($tag['tag']) ?></div>
                                        <small style="font-weight: normal; display:block; line-height: 1.3;">合計 <?php echo number_format($tag['total_member']) ?>人</small>
                                        <small style="font-weight: normal; display:block; line-height: 1.5;"><?php echo number_format($tag['record_count']) ?>件・平均 <?php echo number_format(round($tag['total_member'] / $tag['record_count'])) ?>人</small>
                                    </div>
                                    <div>
                                        <small style="color:#aaa; display:block; margin-left:4px;line-height: 1.3;">1H</small>
                                        <small style="color:#aaa; display:block; margin-left:4px;line-height: 1.3;">24H</small>
                                        <small style="color:#aaa; display:block; margin-left:4px;line-height: 1.3;">1W</small>
                                    </div>
                                    <div>
                                        <small style="display:block; margin-left:4px;line-height: 1.3;"><?php memberCount($tag['hour'] ?? 0) ?></small>
                                        <small style="display:block; margin-left:4px;line-height: 1.3;"><?php memberCount($tag['hour24'] ?? 0) ?></small>
                                        <small style="display:block; margin-left:4px;line-height: 1.3;"><?php memberCount($tag['week'] ?? 0) ?></small>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
                <a style="font-size:15px; text-wrap:nowrap; margin-left:auto; display:inline-flex; color: #111" href="#top">ページの先頭に戻る</a>
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