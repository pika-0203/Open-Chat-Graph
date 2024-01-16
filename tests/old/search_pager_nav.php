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

<?php

//function searchPager(string $keyword, int $pageNumber, string $nameQ = 'q', string $nameP = 'p', string $path = 'search'): string
{
    $query = http_build_query([$nameQ => $keyword]);
    $page = ($pageNumber > 1) ? '&' . http_build_query([$nameP => $pageNumber]) : '';
    return \Shadow\Kernel\Dispatcher\ReceptionInitializer::getDomainAndHttpHost() . "/{$path}?{$query}{$page}";
}
