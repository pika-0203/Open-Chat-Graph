<?php

namespace App\Config;

class OpenChatCrawlerConfig
{
    const USER_AGENT = 'Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Mobile Safari/537.36 (compatible; OpenChatStatsbot; +https://github.com/pika-0203/Open-Chat-Graph)';
    const LINE_URL_MATCH_PATTERN = '{(?<=https:\/\/openchat\.line\.me\/jp\/cover\/).+?(?=\?|$)}';

    const LINE_IMG_URL = 'https://obs.line-scdn.net/';

    const OPEN_CHAT_API_OC_DATA_FROM_EMID_DOWNLOADER_HEADER = [
        "X-Line-Seo-User: x9bfc33ffe50854cf0d446a6013cf1824",
    ];

    static function generateOpenChatApiOcDataFromEmidUrl(string $emid)
    {
        return "https://openchat.line.me/api/square/{$emid}?limit=1";
    }

    static function generateOpenChatApiRankigDataUrl(string $category, string $ct)
    {
        return "https://openchat.line.me/api/category/{$category}?sort=RANKING&limit=40&ct={$ct}";
    }

    static function generateOpenChatApiRisingDataUrl(string $category, string $ct)
    {
        return "https://openchat.line.me/api/category/{$category}?sort=RISING&limit=40&ct={$ct}";
    }
}
