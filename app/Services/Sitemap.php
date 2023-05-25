<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Services\Traits\TraitPaginationRecordsCalculator;
use App\Config\AppConfig;

class Sitemap
{
    use TraitPaginationRecordsCalculator;

    private OpenChatListRepositoryInterface $openChatListRepository;
    private string $siteUrl = 'https://openchat-review.me';
    private array  $pages = [
        ['loc' => ''],
        ['loc' => '/ranking']
    ];

    function __construct(OpenChatListRepositoryInterface $openChatListRepository)
    {
        $this->openChatListRepository = $openChatListRepository;
    }

    function updateSitemap()
    {
        $rankingTotalRecords = $this->openChatListRepository->getRankingRecordCount();
        $rankingMaxPageNumber = $this->calcMaxPages($rankingTotalRecords, AppConfig::OPEN_CHAT_LIST_LIMIT);
        for ($i = 2; $i <= $rankingMaxPageNumber; $i++) {
            $this->pages[] = ['loc' => "/ranking/{$i}"];
        }

        $openChatIdArray = $this->openChatListRepository->getAliveOpenChatIdAll();
        foreach ($openChatIdArray as $oc) {
            $this->pages[] = [
                'loc' => '/oc/' . $oc['id'],
                'lastMod' => $oc['updated_at']
            ];
        }

        $this->createXml();
    }

    private function createXml()
    {
        $sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemapContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        $today = date('Y-m-d');

        foreach ($this->pages as $page) {
            $lastMod = $page['lastMod'] ?? $today;
            $sitemapContent .= "\t<url>" . PHP_EOL;
            $sitemapContent .= "\t\t<loc>" . $this->siteUrl . $page['loc'] . "</loc>" . PHP_EOL;
            $sitemapContent .= "\t\t<lastmod>" . $lastMod . "</lastmod>" . PHP_EOL;
            $sitemapContent .= "\t</url>" . PHP_EOL;
        }

        $sitemapContent .= '</urlset>';

        file_put_contents(AppConfig::SITEMAP_DIR, $sitemapContent);
    }
}
