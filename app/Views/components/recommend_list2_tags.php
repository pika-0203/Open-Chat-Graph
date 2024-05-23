<?php

/** @var \App\Services\Recommend\Dto\RecommendListDto $recommend */

use App\Services\Recommend\Enum\RecommendListType;

$tags = $recommend->getFilterdTags(true)

?>
<?php if ($tags) : ?>
    <hr class="hr-bottom tags-top">
    <article class="top-ranking not-rank" style="<?php echo $style ?? '' ?> padding-top: 0; gap: 0;">
        <header class="openchat-list-title-area unset">
            <div class="openchat-list-date unset ranking-url">
                <?php if ($recommend->type === RecommendListType::Category) : ?>
                    <h2 class="unset">
                        <span style="line-height: 1.5; font-size: 14px; color: #111; font-weight: bold;">おすすめの関連タグ</span>
                    </h2>
                <?php elseif ($recommend->type === RecommendListType::Official) : ?>
                    <h2 class="unset">
                        <span style="line-height: 1.5; font-size: 14px; color: #111; font-weight: bold;">おすすめの関連タグ</span>
                    </h2>
                <?php else : ?>
                    <h2 class="unset">
                        <span style="line-height: 1.5; font-size: 14px; color: #111; font-weight: bold;">おすすめの関連タグ</span>
                    </h2>
                <?php endif ?>
            </div>
        </header>
        <?php viewComponent('tag_list_section', compact('tags')) ?>
    </article>
<?php endif ?>