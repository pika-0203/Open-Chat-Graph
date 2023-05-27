<?php

namespace App\Views;

class Metadata
{
    public string $title = 'オプチャグラフ';

    private string $description =
    'オープンチャットの人数を統計するサイトです。オプチャの成長傾向を分析したり、他のオプチャとの比較が簡単にできます。人数を増やすための手がかりになります！トークルームの運営には必須のツールです。';

    private string $ogpDescription =
    'オープンチャットの人数を統計するサイトです。オプチャの成長傾向を分析したり、他のオプチャとの比較が簡単になります。人数を増やすための手がかりになります！トークルームの運営には必須のツールです。';

    private bool $isTopPageFlag = false;
    private string $image_url = 'https://openchat-review.me/assets/ogp.png';
    public string $site_name = 'オプチャグラフ';
    public string $site_url = 'https://openchat-review.me';
    public string $locale = 'ja_JP';
    public string $og_type;
    public string $ld_type = 'WebSite';

    public function __construct()
    {
        if ($_SERVER["REQUEST_URI"] === '/') {
            $this->og_type = 'website';
        } else {
            $this->og_type = 'article';
        }
    }

    public function setTitle(string $title): static
    {
        $this->title = h($title) . ' | ' . $this->title;
        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = h($description);
        return $this;
    }

    public function setOgpDescription(string $ogpDescription): static
    {
        $this->ogpDescription = h($ogpDescription);
        return $this;
    }

    public function setImageUrl(string $image_url): static
    {
        $this->image_url = h($image_url);
        return $this;
    }

    public function isTopPage(): static
    {
        $this->isTopPageFlag = true;
        return $this;
    }

    public function generateTags(): string
    {
        $tags = '';
        $tags .= '<title>' . $this->title . '</title>' . "\n";
        $tags .= '<meta name="description" content="' . $this->description . '">' . "\n";
        $tags .= '<meta property="og:locale" content="' . $this->locale . '">' . "\n";
        $tags .= '<meta property="og:url" content="' . url(path()) . '">' . "\n";
        $tags .= '<meta property="og:type" content="' . $this->og_type . '">' . "\n";
        $tags .= '<meta property="og:title" content="' . $this->title . '">' . "\n";
        $tags .= '<meta property="og:description" content="' . $this->ogpDescription . '">' . "\n";
        $tags .= '<meta property="og:image" content="' . $this->image_url . '">' . "\n";
        $tags .= '<meta property="og:site_name" content="' . $this->site_name . '">' . "\n";
        $tags .= '<meta name="twitter:card"  content="summary_large_image">' . "\n";

        if (!$this->isTopPageFlag) {
            return $tags;
        }

        $tags .=
            '<script type="application/ld+json">
            {
              "@context": "http://schema.org",
              "@type": "' . $this->ld_type . '",
              "name": "' . $this->site_name . '",
              "url": "' . $this->site_url . '",
              "description": "' . $this->description . '",
              "image": "' . $this->image_url . '"
            }
            </script>' . "\n";

        return $tags;
    }

    public function __toString(): string
    {
        return $this->generateTags();
    }
}
