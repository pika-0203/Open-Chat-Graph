<!-- @param string $q -->
<!-- @param int $pageNumber -->
<!-- @param ing $maxPageNumber -->
<nav class="search-pager">
    <?php if ($pageNumber > 1) : ?>
        <div class="button01 prev">
            <a href="<?php echo searchPager($q, $pageNumber - 1) ?>">
                <?php echo $pageNumber - 1 ?>
                ページへ
            </a>
        </div>
    <?php endif ?>
    <span class="button01label"><?php echo $pageNumber . ' / ' . $maxPageNumber ?></span>
    <?php if ($pageNumber < $maxPageNumber) : ?>
        <div class="button01 next">
            <a href="<?php echo searchPager($q, $pageNumber + 1) ?>">次のページへ</a>
        </div>
    <?php endif ?>
</nav>