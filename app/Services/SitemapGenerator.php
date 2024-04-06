<?php

declare(strict_types=1);

namespace App\Services;

use App\Config\AppConfig;
use Asika\Sitemap\Sitemap;
use Asika\Sitemap\ChangeFreq;
use Asika\Sitemap\SitemapIndex;
use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Services\Recommend\RecommendUpdater;

class SitemapGenerator
{
    const SITE_URL = 'https://openchat-review.me/';
    const SITEMAP_URL = 'https://openchat-review.me/sitemaps/';
    const SITEMAP_DIR = __DIR__ . '/../../public/sitemaps/';
    const INDEX_SITEMAP = __DIR__ . '/../../public/sitemap.xml';

    function __construct(
        private OpenChatListRepositoryInterface $ocRepo,
        private RecommendUpdater $recommendUpdater,
    ) {
    }

    function generate()
    {
        array_map('unlink', glob(self::SITEMAP_DIR . '*.xml'));

        $index = new SitemapIndex();
        $index->addItem($this->generateSitemap1(), new \DateTime);

        foreach (array_chunk($this->ocRepo->getOpenChatSiteMapData(), 25000) as $i => $openChat) {
            $index->addItem($this->genarateOpenChatSitemap($openChat, $i + 2), new \DateTime);
        }

        safeFileRewrite(self::INDEX_SITEMAP, $index->render(), 0755);
    }

    private function generateSitemap1(): string
    {
        $sitemap = new Sitemap();
        $sitemap->addItem(rtrim(self::SITE_URL, "/"), changeFreq: ChangeFreq::DAILY, lastmod: new \DateTime);
        $sitemap->addItem(self::SITE_URL . 'oc');
        $sitemap->addItem(self::SITE_URL . 'policy');
        $sitemap->addItem(self::SITE_URL . 'register');
        $sitemap->addItem(self::SITE_URL . 'ranking');

        foreach (AppConfig::OPEN_CHAT_CATEGORY as $category) {
            $category && $sitemap->addItem(self::SITE_URL . 'ranking/' . $category);
        }
        
        foreach($this->recommendUpdater->getAllTagNames() as $tag) {
            $sitemap->addItem(self::SITE_URL . 'recommend?tag=' . urlencode($tag));
        }

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
        safeFileRewrite(self::SITEMAP_DIR . $fileName, $sitemap->render(), 0755);

        return self::SITEMAP_URL . $fileName;
    }
}
