<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta')) ?>

<body class="body">
    <?php viewComponent('site_header') ?>
    <main>
        <header>
            <span class="main-header-title">OPENCHAT GRAPH</span>
            <span class="main-header-title-desc">メンバー数の増加をグラフで分析</span>
            <small style="font-size: 11px; color:#777"><a href="/policy" style="font-size: inherit; color: inherit">オプチャグラフについてのコメント欄</a>を開設しました</small>
        </header>

        <hr>
        <?php if ($myList) : ?>
            <article class="top-mylist">
                <div class="refresh-time openchat-list-date">
                    <span>ピン留め (1日毎に更新)</span>
                </div>
                <div style="margin: -4px 0 -4px 0;">
                    <?php viewComponent('open_chat_list', ['openChatList' => $myList]) ?>
                </div>
            </article>
            <hr class="ht-top-mylist">
        <?php endif ?>

        <article class="top-list" style="margin-bottom: 2rem;">
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
                        <span><?php echo $hourlyRange ?> の増加</span>
                    </div>
                </div>
            </header>
            <?php /** @var \App\Services\StaticData\Dto\StaticTopPageDto $dto */
            viewComponent('open_chat_list', ['openChatList' => $dto->hourlyList, 'isHourly' => true]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=hourly') ?>">
                <span class="ranking-readMore">もっと見る</span>
            </a>
        </article>

        <article class="top-ranking" style="margin-bottom: -1rem; padding-top: 1.5rem;">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">Daily</span>
                    </h2>
                    <div class="refresh-time">
                        <span>昨日の増加</span>
                    </div>
                </div>
            </header>
            <?php viewComponent('open_chat_list', ['openChatList' => $dto->dailyList]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=daily') ?>">
                <span class="ranking-readMore">もっと見る</span>
            </a>
        </article>

        <article class="top-ranking" style="margin-bottom: -1rem; padding-top: 1.5rem;">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">Weekly</span>
                    </h2>
                    <div class="refresh-time">
                        <span><?php echo $weeklyRange ?> の増加</span>
                    </div>
                </div>
            </header>
            <?php viewComponent('open_chat_list', ['openChatList' => $dto->weeklyList]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=weekly') ?>">
                <span class="ranking-readMore">もっと見る</span>
            </a>
        </article>

        <article class="top-ranking" style="margin-bottom: -1rem; padding-top: 1.5rem;">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">Popular</span>
                    </h2>
                    <div class="refresh-time">
                        <span>メンバーが多い順</span>
                    </div>
                </div>
            </header>
            <?php viewComponent('open_chat_list', ['openChatList' => $dto->popularList]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=all') ?>">
                <span class="ranking-readMore">もっと見る</span>
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