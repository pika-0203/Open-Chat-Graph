<?php

declare(strict_types=1);

namespace App\Services;

use Asika\Sitemap\Sitemap;
use Asika\Sitemap\ChangeFreq;
use Asika\Sitemap\SitemapIndex;
use App\Models\Repositories\OpenChatListRepositoryInterface;

class SitemapGenerator
{
    const SITE_URL = 'https://openchat-review.me/';
    const SITEMAP_URL = 'https://openchat-review.me/sitemaps/';
    const SITEMAP_DIR = __DIR__ . '/../../public/sitemaps/';
    const INDEX_SITEMAP = __DIR__ . '/../../public/sitemap.xml';

    private OpenChatListRepositoryInterface $ocRepo;

    function __construct(OpenChatListRepositoryInterface $ocRepo)
    {
        $this->ocRepo = $ocRepo;
    }

    function generate()
    {
        array_map('unlink', glob(self::SITEMAP_DIR . '*.xml'));

        $index = new SitemapIndex();
        $index->addItem($this->generateSitemap1(), new \DateTime);

        foreach (array_chunk($this->ocRepo->getAliveOpenChatIdAll(), 25000) as $i => $openChat) {
            $index->addItem($this->genarateOpenChatSitemap($openChat, $i + 2), new \DateTime);
        }

        file_put_contents(self::INDEX_SITEMAP, $index->render());
    }

    private function generateSitemap1(): string
    {
        $sitemap = new Sitemap();
        $sitemap->addItem(rtrim(self::SITE_URL, "/"), changeFreq: ChangeFreq::DAILY, lastmod: new \DateTime);
        $sitemap->addItem(self::SITE_URL . 'ranking');

        return $this->saveXml($sitemap, 1);
    }

    private function genarateOpenChatSitemap(array $openChat, int $n): string
    {
        $sitemap = new Sitemap();
        foreach ($openChat as $oc) {
            ['id' => $id, 'updated_at' => $updated_at] = $oc;
            $this->addItem($sitemap, "oc/{$id}", $updated_at);
        }

        return $this->saveXml($sitemap, $n);
    }

    private function addItem(Sitemap $sitemap, string $uri, string $datetime = 'now'): Sitemap
    {
        return $sitemap->addItem(self::SITE_URL . $uri, lastmod: new \DateTime($datetime));
    }

    /**
     * @return string XML URL
     */
    private function saveXml(Sitemap $sitemap, int $n): string
    {
        $fileName = "sitemap{$n}.xml";
        file_put_contents(self::SITEMAP_DIR . $fileName, $sitemap->render());

        return self::SITEMAP_URL . $fileName;
    }
}
