<article class="top-ranking" style="border: 0; padding: 0; margin-top: 1rem; margin-bottom: 1.5rem;">
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
        <span class="ranking-readMore">もっと見る</span>
    </a>
</article>

<div style="margin: 1rem 0;">
    <?php viewComponent('ads/google-full'); ?>
</div>

<article class="top-ranking created-at" style="border: 0; padding: 0; margin: 1rem 0;  margin-bottom: 1.5rem;">
    <header class=" openchat-list-title-area unset">
        <div class="openchat-list-date unset ranking-url">
            <h2 class="unset">
                <span class="openchat-list-title">人数ランキング</span>
            </h2>
            <span style="font-weight: normal; color:#aaa; font-size:13px; margin: 0">※公式運営を除く</span>
        </div>
    </header>
    <?php viewComponent('open_chat_list_ranking', ['openChatList' => $dto->popularList]) ?>
    <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking') ?>">
        <span class="ranking-readMore">もっと見る</span>
    </a>
</article>