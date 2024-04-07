<main>
    <?php if ($recommend[0]) : ?>
        <?php viewComponent('recommend_list', ['recommend' => $recommend[0], 'member' => 0, 'tag' => $recommend[2]]) ?>
        <hr style="margin: 1rem 0;">
    <?php endif ?>
    <?php if ($recommend[1]) : ?>
        <?php viewComponent('recommend_list', ['recommend' => $recommend[1], 'member' => 0, 'tag' => $recommend[2]]) ?>
    <?php endif ?>
    <hr style="margin: .5rem 0;">

    <article class="top-list">
        <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=all&order=desc&sort=member') ?>">
            <span class="ranking-readMore">カテゴリーからオープンチャットを探す</span>
        </a>
    </article>

    <article class="top-ranking">
        <header class="openchat-list-title-area unset">
            <div class="openchat-list-date unset ranking-url">
                <h2 class="unset">
                    <span class="openchat-list-title">Hourly</span>
                </h2>
                <div class="refresh-time">
                    <span>1時間ランキング (<?php echo $hourlyRange ?>)</span>
                </div>
            </div>
        </header>
        <?php /** @var \App\Services\StaticData\Dto\StaticTopPageDto $dto */
        viewComponent('open_chat_list_ranking', ['openChatList' => $dto->hourlyList, 'isHourly' => true]) ?>
        <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=hourly') ?>">
            <span class="ranking-readMore">1時間ランキングを詳しく見る</span>
        </a>
    </article>

    <article class="top-ranking">
        <header class="openchat-list-title-area unset">
            <div class="openchat-list-date unset ranking-url">
                <h2 class="unset">
                    <span class="openchat-list-title">Daily</span>
                </h2>
                <div class="refresh-time">
                    <span>24時間ランキング</span>
                </div>
            </div>
        </header>
        <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->dailyList]) ?>
        <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=daily') ?>">
            <span class="ranking-readMore">24時間ランキングを詳しく見る</span>
        </a>
    </article>

    <article class="top-ranking">
        <header class="openchat-list-title-area unset">
            <div class="openchat-list-date unset ranking-url">
                <h2 class="unset">
                    <span class="openchat-list-title">Weekly</span>
                </h2>
                <div class="refresh-time">
                    <span>1週間ランキング</span>
                </div>
            </div>
        </header>
        <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->weeklyList]) ?>
        <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=weekly') ?>">
            <span class="ranking-readMore">1週間ランキングを詳しく見る</span>
        </a>
    </article>

    <article class="top-ranking created-at">
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
        <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->popularList]) ?>
        <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=all') ?>">
            <span class="ranking-readMore">メンバー数ランキングを詳しく見る</span>
        </a>
    </article>
</main>