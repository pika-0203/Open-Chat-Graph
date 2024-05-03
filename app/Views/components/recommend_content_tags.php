<aside class="list-aside">
    <h3 class="list-title">
        <span>関連のタグ</span>
    </h3>
    <?php if (isset($desc)) : ?>
        <?php viewComponent('recommend_tag_desc') ?>
    <?php endif ?>
    <section class="tag-list-section">
        <ul class="tag-list">
            <?php foreach ($tags as $key => $word) : ?>
                <li>
                    <a class="tag-btn" href="<?php echo url('recommend?tag=' . urlencode(htmlspecialchars_decode($word))) ?>">
                        <?php echo \App\Services\Recommend\RecommendUtility::extractTag($word) ?>
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
    </section>
</aside>