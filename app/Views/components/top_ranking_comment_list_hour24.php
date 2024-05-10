<article class="top-ranking" style="border: 0; padding: 0; margin-top: 1rem; margin-bottom: 1.5rem;">
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
        <span class="ranking-readMore">もっと見る</span>
    </a>
</article>