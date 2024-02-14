<!DOCTYPE html>
<html lang="ja">
<?php viewComponent('head', compact('_css', '_meta')) ?>

<body>
    <?php viewComponent('site_header') ?>
    <main>
        <header>
            <span class="main-header-title">OPENCHAT GRAPH</span>
            <span class="main-header-title-desc">メンバー数の増加をグラフで分析</span>
        </header>

        <hr>
        <?php if ($myList) : ?>
            <article class="top-mylist">
                <?php viewComponent('open_chat_list', ['openChatList' => $myList, 'localUrl' => true]) ?>
            </article>
            <hr class="ht-top-mylist">
        <?php endif ?>

        <article class="top-ranking" style="margin-bottom: -1rem;">
            <header class="openchat-list-title-area unset">
                <div class="openchat-list-date unset ranking-url">
                    <h2 class="unset">
                        <span class="openchat-list-title">Hourly</span>
                    </h2>
                    <div class="refresh-time">
                        <div class="refresh-icon"></div>
                        <time><?php echo $_hourlyUpdatedAt ?></time>
                    </div>
                </div>
            </header>
            <?php viewComponent('open_chat_list', ['openChatList' => array_slice($hourOpenChatList, 0, 5), 'isDaily' => true]) ?>
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
                        <div class="refresh-icon"></div>
                        <time datetime="<?php echo dateTimeAttr($updatedAt) ?>"><?php echo convertDatetime($updatedAt) ?></time>
                    </div>
                </div>
            </header>
            <?php viewComponent('open_chat_list', ['openChatList' => $openChatList, 'isDaily' => true]) ?>
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
                        <div class="refresh-icon"></div>
                        <time datetime="<?php echo dateTimeAttr($updatedAt) ?>"><?php echo convertDatetime($updatedAt) ?></time>
                    </div>
                </div>
            </header>
            <?php viewComponent('open_chat_list', ['openChatList' => $pastWeekOpenChatList, 'isDaily' => false]) ?>
            <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=weekly') ?>">
                <span class="ranking-readMore">もっと見る</span>
            </a>
        </article>

        <p style="padding-top:2rem;">
            <small class="top-small-desc" style="display: block;">オプチャグラフは<a href="https://openchat.line.me/jp/explore?sort=RANKING" rel="external" target="_blank">公式ランキング</a>に掲載中のオープンチャットを自動的に登録して集計します。</small>
        </p>
        <p style="padding-top:8px">
            <a class="recent-oc-btn" href="<?php echo url('recent') ?>">最近登録されたオープンチャット</a>
        </p>
        <p style="padding-top:8px">
            <a class="recent-oc-btn" href="<?php echo url('register') ?>">オープンチャットを手動で登録する</a>
        </p>
    </main>
    <footer>
        <?php viewComponent('footer_inner') ?>
    </footer>
    <script defer src="<?php echo fileurl("/js/site_header_footer.js") ?>"></script>
    <?php echo $_meta->generateTopPageSchema() ?>
</body>

</html>