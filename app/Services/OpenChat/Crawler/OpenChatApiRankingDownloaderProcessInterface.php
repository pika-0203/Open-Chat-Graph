<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

interface OpenChatApiRankingDownloaderProcessInterface
{
    function fetchOpenChatApiRankingProcess(string $category, string $ct, \Closure $callback): array|false;
}
