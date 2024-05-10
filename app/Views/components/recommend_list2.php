<?php

/** @var \App\Services\Recommend\Dto\RecommendListDto $recommend */

use App\Config\AppConfig;
use App\Services\Recommend\Enum\RecommendListType;

?>

<aside class="top-ranking not-rank" style="border: 0; padding: 0; margin-top: 1rem; margin-bottom: 1.5rem;">
    <header class="openchat-list-title-area unset" style="margin-bottom: -1rem;">
        <div class="openchat-list-date unset ranking-url">
            <?php if ($recommend->type === RecommendListType::Category) : ?>
                <h2 class="unset">
                    <span style="line-height: 1.5; font-size: 14px; color: #111; font-weight: bold;">「<?php echo $recommend->listName ?>」カテゴリーのおすすめ</span>
                </h2>
            <?php else : ?>
                <h2 class="unset">
                    <span style="line-height: 1.5; font-size: 14px; color: #111; font-weight: bold;">「<?php echo $recommend->listName ?>」のおすすめ</span>
                </h2>
            <?php endif ?>
        </div>
    </header>
    <?php viewComponent('open_chat_list_recommend', compact('recommend', 'id') + ['limit' => 5, 'shuffle' => true]) ?>
    <?php if ($recommend->type === RecommendListType::Category) : ?>
        <a class="top-ranking-readMore unset ranking-url" href="<?php echo url('ranking/' . AppConfig::OPEN_CHAT_CATEGORY[$recommend->listName] . '?list=hourly') ?>">
            <span class="ranking-readMore">もっと見る</span>
        </a>
    <?php else : ?>
        <a class="top-ranking-readMore unset ranking-url" href="<?php echo url("recommend?tag=" . urlencode(htmlspecialchars_decode($recommend->listName))) ?>">
            <span class="ranking-readMore">もっと見る</span>
        </a>
    <?php endif ?>
</aside>