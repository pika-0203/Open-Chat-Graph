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
viewComponent('head', compact('_css', '_schema', 'canonical') + ['_meta' => $_meta->generateTags(true), 'titleP' => true]) ?>

<body>
    <!-- 固定ヘッダー -->
    <?php viewComponent('site_header') ?>
    <article class="ranking-page-main pad-side-top-ranking body" style="overflow: hidden; padding-top: 0;">
        <?php //viewComponent('ads/google-rectangle') 
        ?>

        <section style="all: unset; display: block;">
            <?php if (isset($recommend)) : ?>
                <figure style="padding: 0;" class="talkroom_banner_img_figure">
                    <?php $oc = $recommend->getPreviewList(1)[0] ?>
                    <div class="talkroom_banner_img_area">
                        <img class="talkroom_banner_img" aria-hidden="true" alt="<?php echo $oc['name'] ?>" src="<?php echo imgUrl($oc['id'], $oc['img_url']) ?>">
                    </div>
                    <figcaption>「<?php echo $oc['name'] ?>」のメイン画像</figcaption>
                </figure>
            <?php endif ?>

            <header class="recommend-header">
                <?php if ($count) : ?>
                    <h1 class="talkroom_link_h1 unset">【最新】「<?php echo $tag ?>」おすすめオープンチャットランキングTOP<?php echo $count ?></h1>
                <?php else : ?>
                    <h1 class="talkroom_link_h1 unset">【最新】「<?php echo $tag ?>」おすすめオープンチャットランキング</h1>
                <?php endif ?>
                <div class="recommend-header-bottom">
                    <div class="recommend-data-desc">統計に基づくランキング</div>
                    <div class="recommend-header-time">
                        <time datetime="<?php echo $_dto->rankingUpdatedAt->format(\DateTime::ATOM) ?>"><?php echo $_dto->rankingUpdatedAt->format('Y年n月j日 G:i') ?></time>
                    </div>
                </div>
            </header>

            <p class="recommend-header-desc">
                「<?php echo $tag ?>」のおすすめオープンチャットランキングを発表！
            </p>
            <p class="recommend-header-desc desc-bottom">
                ランキングの順位は、参加人数がどれぐらい上昇しているかによって決まります。
            </p>
        </section>

        

        <aside class="list-aside recommend-ranking-bottom">
            <?php if (isset($tags) && $tags) : ?>
                <?php viewComponent('recommend_content_tags', compact('tags')) ?>
            <?php endif ?>
        </aside>

        <hr class="hr-bottom" style="padding: 0;">
        <?php //viewComponent('ads/google-rectangle') 
        ?>

        <section class="recommend-ranking-section">
            <?php if (isset($recommend)) : ?>
                <ol class="openchat-item-list parent unset">
                    <?php
                    $chunkLen = 10;
                    $lists = array_chunk($recommend->getList(false), $chunkLen);
                    $listsLastKey = count($lists) - 1;
                    ?>
                    <?php foreach ($lists as $key => $listArray) : ?>
                        <li class="top-ranking">
                            <?php if ($key === 0) : ?>
                                <header class="recommend-ranking-section-header">
                                    <h2 class="list-title oc-list">
                                        <div>「<?php echo $tag ?>」</div>
                                        <div>おすすめランキング</div>
                                        <div><?php echo $countTitle ?></div>
                                        <div>（<?php echo $time ?>）</div>
                                    </h2>
                                    <aside class="list-aside">
                                        <details class="icon-desc">
                                            <summary style="font-size: 13px; font-weight: normal;">人数増加アイコンの説明</summary>
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
                                </header>
                            <?php else : ?>
                                <header class="recommend-ranking-section-header">
                                    <h2 style="all: unset; font-size: 14px; font-weight: bold; color: #111; display: flex; flex-direction:row; flex-wrap:wrap;">
                                        <div>「<?php echo $recommend->listName ?>」</div>
                                        <div>おすすめランキング</div>
                                        <div><?php echo $countTitle ?? '' ?></div>
                                        <div>（<?php echo $key * $chunkLen + 1 ?>位〜）</div>
                                    </h2>
                                </header>
                            <?php endif ?>
                            <?php viewComponent('open_chat_list_recommend', compact('recommend', 'listArray')) ?>
                            <?php if ($listsLastKey === $key && isset($_dto->tagRecordCounts[$_tagIndex]) && ((int)$_dto->tagRecordCounts[$_tagIndex]) > $count) : ?>
                                <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?keyword=' . urlencode('tag:' . $_tagIndex)) ?>">
                                    <span class="ranking-readMore" style="font-size: 11.5px;">「<?php echo $tag ?>」をすべて見る<span class="small" style="font-size: 11.5px;"><?php echo $_dto->tagRecordCounts[$_tagIndex] ?>件</span></span>
                                </a>
                            <?php endif ?>
                        </li>
                        <?php if ($listsLastKey !== $key) : ?>
                            <li>
                                <hr class="hr-bottom">
                                <?php //viewComponent('ads/google-responsive') 
                                ?>
                            </li>
                        <?php endif ?>
                    <?php endforeach ?>
                </ol>
            <?php else : ?>
                <section class="top-ranking recommend-ranking-section">
                    <header class="recommend-ranking-section-header">
                        <h2 class="list-title oc-list">只今サーバー内でリスト更新中です…</h2>
                    </header>
                </section>
            <?php endif ?>

            <hr class="hr-bottom">
            <aside class="list-aside recommend-ranking-bottom">
                <?php if (isset($tags) && $tags) : ?>
                    <?php viewComponent('recommend_content_tags', compact('tags')) ?>
                    <hr class="hr-bottom" style="width: 100%;">
                <?php endif ?>
                <a style="margin: 1rem 0 0 0;" class="readMore-btn top-ranking-readMore unset" href="<?php echo url('ranking') ?>">
                    <span class="ranking-readMore" style="font-size: 11.5px;">カテゴリーからオプチャを探す<span class="small" style="font-size: 11.5px;">24カテゴリー</span></span>
                </a>
            </aside>

        </section>

        <hr class="hr-bottom">
        <?php //viewComponent('ads/google-responsive') 
        ?>

        <aside class="top-ranking-list-aside">
            <?php viewComponent('top_ranking_comment_list_hour24', ['dto' => $rankingDto]) ?>
        </aside>

        <hr class="hr-bottom">
        <?php //viewComponent('ads/google-responsive') 
        ?>

        <aside class="top-ranking-list-aside">
            <?php viewComponent('top_ranking_comment_list_hour', ['dto' => $rankingDto]) ?>
        </aside>
        <hr class="hr-bottom">

        <aside class="unset app_link open-chat-guide">
            <a href="https://openchat-jp.line.me/other/beginners_guide">
                <span class="text">はじめてのLINEオープンチャットガイド（LINE公式）</span>
            </a>
        </aside>

        <?php //viewComponent('ads/google-responsive') 
        ?>

        <footer class="footer-elem-outer">
            <?php viewComponent('footer_share_nav', ['title' => $_meta->title]) ?>
            <?php viewComponent('footer_inner') ?>
        </footer>

    </article>

    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>

    <?php echo $_breadcrumbsShema ?>
</body>

</html>