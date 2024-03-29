<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta', '_schema')) ?>

<body class="body">
    <?php viewComponent('site_header') ?>
    <main>
        <?php if ($myList) : ?>
            <article class="top-mylist">
                <div class="refresh-time openchat-list-date">
                    <span style="font-weight: normal; color:#b7b7b7; font-size:11.5px; margin: 0">ピン留め (1日ごとに更新)</span>
                </div>
                <div style="margin: -4px 0 -4px 0;">
                    <?php viewComponent('open_chat_list', ['openChatList' => $myList]) ?>
                </div>
            </article>
            <hr class="ht-top-mylist">
        <?php endif ?>

        <article class="top-list" style="margin-bottom: 4rem;">
            <a class="top-ranking-readMore unset ranking-url" style="margin-bottom: 1rem;" href="<?php echo url('ranking?list=all&order=desc&sort=member') ?>">
                <span class="ranking-readMore">カテゴリーからオープンチャットを探す</span>
            </a>
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">Comments</span>
                    </h2>
                    <div class="refresh-time">
                        <span>最近のコメント投稿</span>
                    </div>
                </div>
            </header>
            <?php /** @var \App\Services\StaticData\Dto\StaticTopPageDto $dto */
            viewComponent('open_chat_list', ['openChatList' => $dto->recentCommentList]) ?>
        </article>

        <article class="top-ranking" style="margin-bottom: -1rem;">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">Hourly</span>
                    </h2>
                    <div class="refresh-time">
                        <span><?php echo $hourlyRange ?> の増加ランキング</span>
                    </div>
                </div>
            </header>
            <?php /** @var \App\Services\StaticData\Dto\StaticTopPageDto $dto */
            viewComponent('open_chat_list', ['openChatList' => $dto->hourlyList, 'isHourly' => true]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=hourly') ?>">
                <span class="ranking-readMore">ランキングをもっと見る</span>
            </a>
        </article>

        <article class="top-ranking" style="margin-bottom: -1rem; padding-top: 1.5rem;">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">Daily</span>
                    </h2>
                    <div class="refresh-time">
                        <span>昨日の増加ランキング</span>
                    </div>
                </div>
            </header>
            <?php viewComponent('open_chat_list', ['openChatList' => $dto->dailyList]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=daily') ?>">
                <span class="ranking-readMore">ランキングをもっと見る</span>
            </a>
        </article>

        <article class="top-ranking" style="margin-bottom: -1rem; padding-top: 1.5rem;">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">Weekly</span>
                    </h2>
                    <div class="refresh-time">
                        <span><?php echo $weeklyRange ?> の増加ランキング</span>
                    </div>
                </div>
            </header>
            <?php viewComponent('open_chat_list', ['openChatList' => $dto->weeklyList]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=weekly') ?>">
                <span class="ranking-readMore">ランキングをもっと見る</span>
            </a>
        </article>

        <article class="top-ranking created-at" style="margin-bottom: -1rem; padding-top: 1.5rem;">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">Popular</span>
                    </h2>
                    <div class="refresh-time">
                        <span>メンバー数ランキング</span>
                    </div>
                    <span style="font-weight: normal; color:#b7b7b7; font-size:11.5px; margin: 0">※公式運営を除く</span>
                </div>
            </header>
            <?php viewComponent('open_chat_list', ['openChatList' => $dto->popularList]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=all') ?>">
                <span class="ranking-readMore">ランキングをもっと見る</span>
            </a>
        </article>

        <p style="padding-top:2rem;">
            <small class="top-small-desc" style="display: block;">オプチャグラフは<a href="https://openchat.line.me/jp/explore?sort=RANKING" rel="external" target="_blank">公式ランキング</a>に掲載中のオープンチャットを自動的に登録して集計します。</small>
        </p>
        <p style="padding-top:8px">
            <a class="recent-oc-btn" href="<?php echo url('oc') ?>">最近登録されたオープンチャット</a>
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