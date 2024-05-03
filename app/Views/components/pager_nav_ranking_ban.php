<!-- @param string $path -->
<!-- @param array $params -->
<!-- @param int $pageNumber -->
<!-- @param ing $maxPageNumber -->
<nav class="search-pager">
    <?php

    use App\Views\RankingBanSelectElementPagination;

    if ($pageNumber > 1) : ?>
        <div class="button01 prev">
            <a href="<?php echo RankingBanSelectElementPagination::pagerUrl($path, $pageNumber - 1, $params) ?>">
                <?php echo $pageNumber - 1 ?>
                ページへ
            </a>
        </div>
    <?php endif ?>
    <span class="button01label"><?php echo $pageNumber . ' / ' . $maxPageNumber ?></span>
    <?php if ($pageNumber < $maxPageNumber) : ?>
        <div class="button01 next">
            <a href="<?php echo RankingBanSelectElementPagination::pagerUrl($path, $pageNumber + 1, $params) ?>">次のページへ</a>
        </div>
    <?php endif ?>
</nav>