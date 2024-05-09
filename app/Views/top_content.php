<!DOCTYPE html>
<html lang="ja">
<?php

use App\Services\Recommend\RecommendUtility;

/** @var \App\Services\StaticData\Dto\StaticTopPageDto $dto */
viewComponent('head', compact('_css', '_meta', '_schema')) ?>

<body class="body">
    <?php viewComponent('site_header', compact('_updatedAt')) ?>
    <main style="margin-bottom: 0;">
        <article class="top-ranking" style="padding-top: 0rem; margin-top: 0; margin-bottom: 1rem; padding-bottom: 1rem;">
            <a style="margin-bottom: 0;" class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking') ?>">
                <span class="ranking-readMore">カテゴリーからオプチャを探す<span class="small">24カテゴリー</span></span>
            </a>

            <a class="top-ranking-readMore unset" style="margin:0" href="<?php echo url('labs') ?>">
                <span class="ranking-readMore" style="display: flex; align-items: center;">
                    <svg style="color: #111; fill: currentColor; display: inline-block; margin-right: 4px" focusable="false" height="18px" viewBox="0 -960 960 960" width="18px">
                        <path d="M209-120q-42 0-70.5-28.5T110-217q0-14 3-25.5t9-21.5l228-341q10-14 15-31t5-34v-110h-20q-13 0-21.5-8.5T320-810q0-13 8.5-21.5T350-840h260q13 0 21.5 8.5T640-810q0 13-8.5 21.5T610-780h-20v110q0 17 5 34t15 31l227 341q6 9 9.5 20.5T850-217q0 41-28 69t-69 28H209Zm221-660v110q0 26-7.5 50.5T401-573L276-385q-6 8-8.5 16t-2.5 16q0 23 17 39.5t42 16.5q28 0 56-12t80-47q69-45 103.5-62.5T633-443q4-1 5.5-4.5t-.5-7.5l-78-117q-15-21-22.5-46t-7.5-52v-110H430Z"></path>
                    </svg>
                    <span style="display: inline-block; line-height: 1;">分析Labs</span>
                </span>
            </a>

            <?php if ($tags) : ?>
                <div style="margin-top: 1rem;">
                    <header class="openchat-list-title-area unset" style="margin-bottom: 10px;">
                        <div class="openchat-list-date unset ranking-url">
                            <h2 class="unset">
                                <span class="openchat-list-title">いま人数急増中のテーマ</span>
                                <span aria-hidden="true" style="font-size: 9px; user-select: none; margin-bottom: px;margin-left: -3px;">🚀</span>
                            </h2>
                            <span style="font-weight: normal; color:#aaa; font-size:13px; margin: 0"><?php echo $dto->hourlyUpdatedAt->format('G:i') ?></span>
                        </div>
                    </header>

                    <ul class="tag-list">
                        <?php foreach ($tags['hour'] as $key => $word) : ?>
                            <li>
                                <a class="hour tag-btn" href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($word))) ?>">
                                    <?php echo RecommendUtility::extractTag($word) ?>
                                    <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                                        <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
                                    </svg>
                                </a>
                            </li>
                        <?php endforeach ?>
                        <?php foreach ($tags['hour24'] as $key => $word) : ?>
                            <li>
                                <a class="tag-btn" href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($word))) ?>">
                                    <?php echo RecommendUtility::extractTag($word) ?>
                                </a>
                            </li>
                        <?php endforeach ?>
                        <?php if (count($tags['hour']) + count($tags['hour24']) > 13) : ?>
                            <li id="open-btn-li">
                                <button class="unset tag-btn open-btn" onclick="this.parentElement.parentElement.classList.toggle('open')"></button>
                            </li>
                        <?php endif ?>
                    </ul>
                </div>
            <?php endif ?>
        </article>

        <?php if ($myList) : ?>
            <article class="mylist">
                <div class="refresh-time openchat-list-date">
                    <span style="font-weight: bold; color:#111; font-size:13px; margin: 0; line-height: unset;">ピン留め (24時間の人数増加)</span>
                    <span style="font-weight: normal; color:#aaa; font-size:13px; margin: 0; line-height: unset;"><?php echo $dto->hourlyUpdatedAt->format('G:i') ?></span>
                </div>
                <div style="margin: -4px 0 -4px 0;">
                    <?php viewComponent('open_chat_list_ranking', ['openChatList' => $myList, 'isHourly' => true]) ?>
                </div>
            </article>
            <hr class="ht-top-mylist">
        <?php endif ?>

        <div style="margin: 2rem 0;">
            <?php viewComponent('top_ranking_comment_list_hour', compact('dto')) ?>
        </div>

        <div style="margin: 1rem 0;">
            <?php viewComponent('ads/google-full'); ?>
        </div>

        <div style="margin: 2rem 0;">
            <?php viewComponent('top_ranking_comment_list_hour24', compact('dto')) ?>
        </div>

        <div style="margin: 1rem 0;">
            <?php viewComponent('ads/google-full'); ?>
        </div>

        <div style="margin: 2rem 0;">
            <?php viewComponent('top_ranking_recent_comments', compact('dto')) ?>
        </div>

        <div style="margin: 1rem 0;">
            <?php viewComponent('ads/google-full'); ?>
        </div>

        <div style="margin: 2rem 0;">
            <?php viewComponent('top_ranking_comment_list_2', compact('dto')) ?>
        </div>

        <article class="top-ranking" style="padding-top: 0; margin-top: 2rem; border: 0">
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('policy') ?>">
                <span class="ranking-readMore">オプチャグラフについて</span>
            </a>
        </article>
    </main>
    <footer>
        <?php viewComponent('footer_share_nav', ['title' => $_meta->title]) ?>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <?php echo $_meta->generateTopPageSchema() ?>
</body>

</html>