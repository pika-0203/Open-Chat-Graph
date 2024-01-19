<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Crawler;;

class OpenChatApiRankingDownloaderProcess extends AbstractOpenChatApiRankingDownloaderProcess
{
    protected string $callableGenerateUrl = '\App\Config\OpenChatCrawlerConfig::generateOpenChatApiRankigDataUrl';
}
