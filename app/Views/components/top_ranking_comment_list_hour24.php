<article class="top-ranking" style="border: 0; padding: 0; margin-top: 1rem;">
    <header class="openchat-list-title-area unset">
        <div class="openchat-list-date unset ranking-url">
            <h2 class="unset">
                <span class="openchat-list-title">24時間の人数増加ランキング</span>
            </h2>
            <span style="font-weight: normal; color:#777; font-size:13px; margin: 0">1時間ごとに更新</span>
        </div>
    </header>
    <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->dailyList, 'isHourly' => true]) ?>
    <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking?list=daily') ?>">
        <span class="ranking-readMore">24時間の人数増加ランキングをすべて見る</span>
    </a>
    <div>
        <?php viewComponent('ads/google-full-display') ?>
    </div>
</article>