<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta', '_schema')) ?>

<body class="body">
    <?php viewComponent('site_header') ?>
    <main style="margin-bottom: 0;">
        <article class="top-ranking" style="padding-top: 0; margin-top: 0; margin-bottom: 1rem">
            <a style="margin-bottom: 0;" class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking') ?>">
                <span class="ranking-readMore">カテゴリーからオープンチャットを探す</span>
            </a>
            <?php if ($tags) : ?>
                <div>
                    <header class="openchat-list-title-area unset">
                        <div class="openchat-list-date unset ranking-url">
                            <h2 class="unset">
                                <span class="openchat-list-title">いま人数増加中のキーワード</span>
                                <span aria-hidden="true" style="font-size: 9px; user-select: none; margin-bottom: px;margin-left: -3px;">🚀</span>
                            </h2>
                            <span style="font-weight: normal; color:#b7b7b7; font-size:13px; margin: 0"><?php echo $hourlyEnd ?></span>
                        </div>
                    </header>
                    <ul class="tag-list">
                        <?php foreach ($tags['hour'] as $key => $word) : ?>
                            <li>
                                <a class="hour tag-btn" href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($word))) ?>">
                                    <?php echo \App\Services\Recommend\RecommendUtility::extractTag($word) ?>
                                    <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeMedium show-north css-162gv95" focusable="false" aria-hidden="true" viewBox="0 0 24 24" data-testid="NorthIcon">
                                        <path d="m5 9 1.41 1.41L11 5.83V22h2V5.83l4.59 4.59L19 9l-7-7-7 7z"></path>
                                    </svg>
                                </a>
                            </li>
                        <?php endforeach ?>
                        <?php foreach ($tags['hour24'] as $key => $word) : ?>
                            <li>
                                <a class="tag-btn" href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($word))) ?>">
                                    <?php echo \App\Services\Recommend\RecommendUtility::extractTag($word) ?>
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
                    <span style="font-weight: normal; color:#b7b7b7; font-size:13px; margin: 0; line-height: unset;"><?php echo $hourlyEnd ?></span>
                </div>
                <div style="margin: -4px 0 -4px 0;">
                    <?php viewComponent('open_chat_list_ranking', ['openChatList' => $myList, 'isHourly' => true]) ?>
                </div>
                <aside style="font-size: 13px; display: block; margin:1rem 0;" class="unset">
                    <details style="margin:0 0 0 0; width:100%;">
                        <summary class="news-summary">
                            <span>アップデート情報</span>
                            <span style="color: #b7b7b7; font-weight:normal; font-size:13px"><?php /** @var \App\Views\Content\TopPageNews[] $news */ echo timeElapsedString($news[0]->date->format('Y-m-d H:i:s')) ?></span>
                        </summary>
                        <div style="position:relative;">
                            <div style="margin: .5rem 0 .5rem 0; max-height: 20rem; overflow-y: auto;">
                                <div style="margin-bottom: 2rem;">
                                    <?php foreach ($news as $el) : ?>
                                        <div style="margin-bottom: 1rem; border-bottom: 1px solid #efefef; width: 100%;">
                                            <span style="color: #111; font-size: 13px; font-weight: bold"><?php echo $el->title ?></span>
                                            <span style="color: #777; margin-left: 4px"><?php echo $el->date->format('Y/n/j G:i') ?></span>
                                            <?php foreach ($el->body as $body) : ?>
                                                <?php if (is_array($body)) : ?>
                                                    <ul style="padding-left: 1rem;">
                                                        <?php foreach ($body as $li) : ?>
                                                            <li style="white-space: pre-line;"><?php echo $li ?></li>
                                                        <?php endforeach ?>
                                                    </ul>
                                                <?php else : ?>
                                                    <p style="white-space: pre-line;"><?php echo $body ?></p>
                                                <?php endif ?>
                                            <?php endforeach ?>
                                        </div>
                                    <?php endforeach ?>
                                </div>
                            </div>
                            <div class="gradient-bottom"></div>
                        </div>
                    </details>
                </aside>
            </article>
            <hr class="ht-top-mylist">
        <?php endif ?>

        <article class="top-ranking" style="padding-top: 0; margin-top: 0;">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">1時間の人数増加ランキング</span>
                    </h2>
                    <span style="font-weight: normal; color:#b7b7b7; font-size:13px; margin: 0"><?php echo $_hourlyRange ?></span>
                </div>
            </header>
            <?php /** @var \App\Services\StaticData\Dto\StaticTopPageDto $dto */
            viewComponent('open_chat_list_ranking', ['openChatList' => $dto->hourlyList, 'isHourly' => true]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=hourly') ?>">
                <span class="ranking-readMore">1時間の人数増加ランキングを詳しく見る</span>
            </a>
        </article>

        <article class="top-list">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">最近のコメント投稿</span>
                    </h2>
                </div>
            </header>
            <?php /** @var \App\Services\StaticData\Dto\StaticTopPageDto $dto */
            viewComponent('open_chat_list_ranking_comment', ['openChatList' => $dto->recentCommentList]) ?>
        </article>

        <article class="top-ranking">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">24時間の人数増加ランキング</span>
                    </h2>
                    <span style="font-weight: normal; color:#b7b7b7; font-size:13px; margin: 0">1時間ごとに更新</span>
                </div>
            </header>
            <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->dailyList, 'isHourly' => true]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=daily') ?>">
                <span class="ranking-readMore">24時間の人数増加ランキングを詳しく見る</span>
            </a>
        </article>

        <article class="top-ranking">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">1週間の人数増加ランキング</span>
                    </h2>
                    <span style="font-weight: normal; color:#b7b7b7; font-size:13px; margin: 0">1日ごとに更新</span>
                </div>
            </header>
            <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->weeklyList]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=weekly') ?>">
                <span class="ranking-readMore">1週間の人数増加ランキングを詳しく見る</span>
            </a>
        </article>

        <article class="top-ranking created-at">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">人数ランキング</span>
                    </h2>
                    <span style="font-weight: normal; color:#b7b7b7; font-size:13px; margin: 0">※公式運営を除く</span>
                </div>
            </header>
            <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->popularList]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking') ?>">
                <span class="ranking-readMore">人数ランキングを詳しく見る</span>
            </a>
        </article>

        <article class="top-ranking" style="padding-top: 0; margin-top: 0; border: 0">
            <p style="line-height: 2; margin: 1.5rem 0 0 0;" class="top-small-desc">
                オプチャグラフは<a href="https://openchat.line.me/jp/" rel="external" target="_blank">LINEオープンチャット公式サイト</a>に掲載されているオープンチャットを記録しています。
            </p>
            <a style="margin-bottom: 0; margin-top: 10px;" class="top-ranking-readMore unset ranking-url" href="<?php echo url('policy') ?>">
                <span class="ranking-readMore">オプチャグラフについて</span>
            </a>
        </article>
        <?php if (!$myList) : ?>
            <aside style="font-size: 13px; display: block; margin:0;" class="unset">
                <details style="margin:0 0 0 0; width:100%;">
                    <summary class="news-summary">
                        <span>アップデート情報</span>
                        <span style="color: #b7b7b7; font-weight:normal; font-size:13px"><?php /** @var \App\Views\Content\TopPageNews[] $news */ echo timeElapsedString($news[0]->date->format('Y-m-d H:i:s')) ?></span>
                    </summary>
                    <div style="position:relative;">
                        <div style="margin: .5rem 0 .5rem 0; max-height: 20rem; overflow-y: auto;">
                            <div style="margin-bottom: 2rem;">
                                <?php foreach ($news as $el) : ?>
                                    <div style="margin-bottom: 1rem; border-bottom: 1px solid #efefef; width: 100%;">
                                        <span style="color: #111; font-size: 13px; font-weight: bold"><?php echo $el->title ?></span>
                                        <span style="color: #777; margin-left: 4px"><?php echo $el->date->format('Y/n/j G:i') ?></span>
                                        <?php foreach ($el->body as $body) : ?>
                                            <?php if (is_array($body)) : ?>
                                                <ul style="padding-left: 1rem;">
                                                    <?php foreach ($body as $li) : ?>
                                                        <li style="white-space: pre-line;"><?php echo $li ?></li>
                                                    <?php endforeach ?>
                                                </ul>
                                            <?php else : ?>
                                                <p style="white-space: pre-line;"><?php echo $body ?></p>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>
                        <div class="gradient-bottom"></div>
                    </div>
                </details>
            </aside>
        <?php endif ?>
    </main>
    <footer>
        <?php viewComponent('footer_share_nav', ['title' => $_meta->title]) ?>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <?php echo $_meta->generateTopPageSchema() ?>
</body>

</html>