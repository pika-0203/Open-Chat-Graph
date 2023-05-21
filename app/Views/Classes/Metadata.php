<?php

namespace App\Views;

class Metadata
{
    public string $title = 'オプチャグラフ';

    private string $description =
    'LINEオープンチャットで作られたトークルームのメンバー数推移をグラフで表示するサービスです。 トークルームの人数変化を視覚的に確認することができます！ 統計から成長傾向を振り返ったり、他のオプチャとの比較が出来ることで、管理者の方にとっても運営の手助けになります！';

    private string $ogpDescription =
    'オプチャのメンバー数遷移をグラフで確認できます。 統計から成長傾向を振り返ったり、他のオプチャとの比較が出来ることで、管理者の方にとっても運営の手助けになります！';

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
        $this->title = $this->h($title) . ' | ' . $this->title;
        return $this;
    }

    public function setDescription(string $description): static
    {
        $this->description = $this->h($description);
        return $this;
    }

    public function setOgpDescription(string $ogpDescription): static
    {
        $this->ogpDescription = $this->h($ogpDescription);
        return $this;
    }

    public function setImageUrl(string $image_url): static
    {
        $this->image_url = $this->h($image_url);
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
              "image": "' . $this->image_url . '",
              "potentialAction": {
                "@type": "SearchAction",
                "target": {
                  "@type": "EntryPoint",
                  "urlTemplate": "' . $this->site_url . '/search?q={search_term_string}"
                },
                "query-input": "required name=search_term_string"
              }
            }
            </script>' . "\n";

        return $tags;
    }

    public function __toString(): string
    {
        return $this->generateTags();
    }

    private function h(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
