<main>
    <article class="top-list">
        <a class="top-ranking-readMore unset ranking-url" style="margin-bottom: 1rem;" href="<?php echo url('ranking?list=all&order=desc&sort=member') ?>">
            <span class="ranking-readMore">カテゴリーからオープンチャットを探す</span>
        </a>
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
</main>