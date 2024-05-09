<!DOCTYPE html>
<html lang="ja">
<?php

use App\Config\AppConfig;

$_tagIndex = htmlspecialchars_decode($tag);
if (isset($_dto->tagRecordCounts[$_tagIndex])) {
    $countTitle = ((int)$_dto->tagRecordCounts[$_tagIndex]) > $count ? 'TOP' . $count : '全' . $count . '件';
} else {
    $countTitle = '';
}

/** @var \App\Services\StaticData\Dto\StaticRecommendPageDto $_dto */
viewComponent('head', compact('_css', '_schema', 'canonical') + ['_meta' => $_meta->generateTags(true)]) ?>

<body class="body">
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header', ['_updatedAt' => $_dto->rankingUpdatedAt]) ?>
    <main class="ranking-page-main">
        <article>
            <header class="recommend-header">
                <div class="hearder-img-outer">
                    <div class="header-img">
                        <?php if (isset($recommend)) : ?>
                            <?php foreach ($recommend->getPreviewList(8) as $oc) : ?>
                                <img alt="<?php echo $oc['name'] ?>" src="<?php echo imgUrl($oc['id'], $oc['img_url']) ?>">
                            <?php endforeach ?>
                        <?php endif ?>
                    </div>
                    <div class="header-img-title">
                        <?php if ($count) : ?>
                            <h2>【<?php echo $tag ?>】オープンチャット人数急増ランキングTOP<?php echo $count ?>【<?php echo $time ?>】</h2>
                        <?php else : ?>
                            <h2>【<?php echo $tag ?>】オープンチャット人数急増ランキング【<?php echo $time ?>】</h2>
                        <?php endif ?>
                    </div>
                </div>
            </header>

            <section class="unset close" style="display: block; position:relative;" id="recommed-desc-box">
                <p class="recommend-desc recommend-p" id="recommed-desc">
                    <span class="recommend-desc-child">「<?php echo $extractTag ?>」をテーマにした中で、最近人数が急増しているルームをご紹介🙌</span>
                </p>
            </section>

            <div class="top-ranking" style="margin: 0; padding: 0; border: 0; display: block; margin-top: 2rem;">
                <?php if ($count) : ?>
                    <h2 class="list-title oc-list">
                        <div>「<?php echo $tag ?>」の</div>
                        <div>人数急増ランキング</div>
                        <div><?php echo $countTitle ?></div>
                        <div>【<?php echo $time ?>】</div>
                    </h2>
                    <aside class="list-aside" style="margin:0 0 1.25rem 0;">
                        <details class="icon-desc">
                            <summary style="font-size: 14px;">メンバー数のアイコンについて</summary>
                            <div class="list-aside-details">
                                <small class="list-aside-desc">🔥：過去1時間で<?php echo AppConfig::MIN_MEMBER_DIFF_HOUR ?>人以上増加<?php if (count($recommend->hour) >= AppConfig::RECOMMEND_LIST_LIMIT) : ?> (<?php echo AppConfig::RECOMMEND_LIST_LIMIT ?>件まで)<?php endif ?></small>
                                <small class="list-aside-desc">🚀：過去24時間で<?php echo AppConfig::MIN_MEMBER_DIFF_H24 ?>人以上増加<?php if (count($recommend->day) >= AppConfig::RECOMMEND_LIST_LIMIT) : ?> (<?php echo AppConfig::RECOMMEND_LIST_LIMIT ?>件まで)<?php endif ?></small>
                                <small class="list-aside-desc">
                                    <span style="margin: 0 4px;">
                                        <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                                            <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
                                        </svg>
                                    </span>：過去1週間で<?php echo AppConfig::MIN_MEMBER_DIFF_WEEK ?>人以上増加<?php if (count($recommend->week) >= AppConfig::RECOMMEND_LIST_LIMIT) : ?> (上位<?php echo AppConfig::RECOMMEND_LIST_LIMIT ?>件まで)<?php endif ?>
                                </small>
                                <small class="list-aside-desc">🏆：リスト内で最も人数が多いトークルーム</small>
                            </div>
                        </details>
                    </aside>
                <?php else : ?>
                    <h2 class="list-title oc-list">只今サーバー内でリスト更新中です…</h2>
                <?php endif ?>
                <?php if (isset($recommend)) : ?>
                    <?php viewComponent('open_chat_list_recommend', compact('recommend')) ?>
                <?php endif ?>
            </div>
            <?php if (isset($_dto->tagRecordCounts[$_tagIndex]) && ((int)$_dto->tagRecordCounts[$_tagIndex]) > $count) : ?>
                <div class="top-list" style="margin: 0 0 0 0; border: 0; padding: 1rem 0 0 0;">
                    <a style="margin: 0;" class="top-ranking-readMore unset" href="<?php echo url('ranking?keyword=' . urlencode('tag:' . $_tagIndex)) ?>">
                        <span class="ranking-readMore" style="font-size: 11.5px;">「<?php echo $tag ?>」をすべて見る<span class="small" style="font-size: 11.5px;"><?php echo $_dto->tagRecordCounts[$_tagIndex] ?>件</span></span>
                    </a>
                </div>
            <?php endif ?>

            <?php if (isset($tags) && $tags) : ?>
                <?php viewComponent('recommend_content_tags', compact('tags')) ?>
            <?php endif ?>

            <div class="top-list" style="margin-bottom: 0rem; padding: 0; border: 0">
                <a style="margin: 1rem 0;" class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking') ?>">
                    <span class="ranking-readMore" style="font-size: 11.5px;">カテゴリーからオプチャを探す<span class="small" style="font-size: 11.5px;">24カテゴリー</span></span>
                </a>
            </div>

            <aside style="all: unset; display:block; margin: 20px 0 0 0; text-align: center;">
                <div class="app_link">
                    <a href="https://openchat-jp.line.me/other/beginners_guide">
                        <span class="text">はじめてのLINEオープンチャットガイド（LINE公式）</span>
                    </a>
                </div>
            </aside>
        </article>
    </main>
    <footer style="padding-top: 1rem;">
        <?php viewComponent('footer_share_nav', ['title' => $_meta->title]) ?>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>

    <?php echo $_breadcrumbsShema ?>
</body>

</html>