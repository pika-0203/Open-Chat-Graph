<article class="top-list" style="padding-top: 0; padding-bottom: 1rem;">
    <header class="openchat-list-title-area unset">
        <div class="openchat-list-date unset ranking-url">
            <h2 class="unset">
                <span class="openchat-list-title">最近のコメント投稿</span>
            </h2>
        </div>
    </header>
    <?php viewComponent('open_chat_list_ranking_comment', ['openChatList' => $dto->recentCommentList]) ?>
    <div style="margin-top: 1rem;">
        <?php viewComponent('ads/google-full-display') ?>
    </div>
</article>