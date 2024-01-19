<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;

class OpenChatApiRisingDownloaderProcess extends AbstractOpenChatApiRankingDownloaderProcess
{
    protected string $callableGenerateUrl = '\App\Config\OpenChatCrawlerConfig::generateOpenChatApiRisingDataUrl';
}
