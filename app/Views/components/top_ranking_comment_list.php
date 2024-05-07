<?php
$hourlyTime = $dto->hourlyUpdatedAt->format(\DateTime::ATOM);
$hourlyEnd = $dto->hourlyUpdatedAt->format('G:i');
$dto->hourlyUpdatedAt->modify('-1hour');
$hourlyStart = $dto->hourlyUpdatedAt->format('G:i');
$_hourlyRange = $hourlyStart . '〜<time datetime="' . $hourlyTime . '">' . $hourlyEnd . '</time>';
?>

<article class="top-ranking">
    <header class="openchat-list-title-area unset">
        <div class="openchat-list-date unset ranking-url">
            <h2 class="unset">
                <span class="openchat-list-title">1時間の人数増加ランキング</span>
            </h2>
            <span style="font-weight: normal; color:#aaa; font-size:13px; margin: 0">
                <?php echo $_hourlyRange ?>
            </span>
        </div>
    </header>
    <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->hourlyList, 'isHourly' => true]) ?>
    <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=hourly') ?>">
        <span class="ranking-readMore">1時間の人数増加ランキングをすべて見る</span>
    </a>
    <div style="margin-top: 1rem;">
        <?php viewComponent('ads/google-full-display') ?>
    </div>
</article>

<article class="top-ranking">
    <header class="openchat-list-title-area unset">
        <div class="openchat-list-date unset ranking-url">
            <h2 class="unset">
                <span class="openchat-list-title">24時間の人数増加ランキング</span>
            </h2>
            <span style="font-weight: normal; color:#aaa; font-size:13px; margin: 0">1時間ごとに更新</span>
        </div>
    </header>
    <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->dailyList, 'isHourly' => true]) ?>
    <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=daily') ?>">
        <span class="ranking-readMore">24時間の人数増加ランキングをすべて見る</span>
    </a>
    <div style="margin-top: 1rem;">
        <?php viewComponent('ads/google-full-display') ?>
    </div>
</article>

<article class="top-ranking">
    <header class="openchat-list-title-area unset">
        <div class="openchat-list-date unset ranking-url">
            <h2 class="unset">
                <span class="openchat-list-title">1週間の人数増加ランキング</span>
            </h2>
            <span style="font-weight: normal; color:#aaa; font-size:13px; margin: 0">1日ごとに更新</span>
        </div>
    </header>
    <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->weeklyList]) ?>
    <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=weekly') ?>">
        <span class="ranking-readMore">1週間の人数増加ランキングをすべて見る</span>
    </a>
    <div style="margin-top: 1rem;">
        <?php viewComponent('ads/google-full-display') ?>
    </div>
</article>

<article class="top-ranking created-at">
    <header class="openchat-list-title-area unset">
        <div class="openchat-list-date unset ranking-url">
            <h2 class="unset">
                <span class="openchat-list-title">人数ランキング</span>
            </h2>
            <span style="font-weight: normal; color:#aaa; font-size:13px; margin: 0">※公式運営を除く</span>
        </div>
    </header>
    <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->popularList]) ?>
    <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking') ?>">
        <span class="ranking-readMore">人数ランキングをすべて見る</span>
    </a>
    <div style="margin-top: 1rem;">
        <?php viewComponent('ads/google-fluid-h82') ?>
    </div>
</article>