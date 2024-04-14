<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta', '_schema')) ?>

<body class="body">
    <?php viewComponent('site_header') ?>
    <main>
        <?php if ($myList) : ?>
            <article>
                <div class="refresh-time openchat-list-date">
                    <span style="font-weight: normal; color:#b7b7b7; font-size:11.5px; margin: 0">ピン留め (過去24時間の増減)</span>
                </div>
                <div style="margin: -4px 0 -4px 0;">
                    <?php viewComponent('open_chat_list', ['openChatList' => $myList]) ?>
                </div>
            </article>
            <hr class="ht-top-mylist">
        <?php endif ?>

        <article class="top-ranking" style="padding-top: 0; margin-top: 0; margin-bottom: 1rem">
            <a style="margin-bottom: 0;" class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking') ?>">
                <span class="ranking-readMore">カテゴリーからオープンチャットを探す</span>
            </a>
            <div>
                <h2 class="unset">
                    <span class="openchat-list-title">いま話題のキーワード</span>
                </h2>
                <ul class="tag-list">
                    <?php foreach ($tags as $key => $word) : ?>
                        <li>
                            <a class="tag-btn" href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($word))) ?>">
                                <?php echo extractTag($word) ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>
            </div>
        </article>

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

        <p style="padding-top:8px">
            <small class="top-small-desc" style="display: block;">オプチャグラフは<a href="https://openchat.line.me/jp/explore?sort=RANKING" rel="external" target="_blank">公式ランキング</a>に掲載中のオープンチャットを自動的に登録して集計します。</small>
        </p>
        <p style="padding-top:8px">
            <a class="recent-oc-btn" href="<?php echo url('recently-registered') ?>">最近登録されたオープンチャット</a>
        </p>
        <p style="padding-top:8px">
            <a class="recent-oc-btn" href="<?php echo url('register') ?>">オープンチャットを手動で登録する</a>
        </p>
    </main>
    <footer>
        <?php viewComponent('footer_share_nav', ['title' => $_meta->title]) ?>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <?php echo $_meta->generateTopPageSchema() ?>
</body>

</html>