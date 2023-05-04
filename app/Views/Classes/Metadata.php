<?php

namespace App\Views;

class Metadata
{
    private string $title = 'LINE オープンチャットグラフ';

    private string $description =
    'LINE OpenChatで作られたトークルームのメンバー数推移をグラフで表示するサービスです。
    トークルームのメンバー数がどのように変化しているかを視覚的に確認することができます！
    オプチャの成長傾向を振り返ったり、他のオプチャとの比較が出来ることで、管理者の方にとっても運営の手助けになります！';

    private string $ogpDescription =
    'オプチャのメンバー数遷移をグラフで視覚的に確認することができます。
    オプチャの成長傾向を振り返ったり、他のオプチャとの比較が出来ることで、管理者の方にとっても運営の手助けになります！';

    private string $image_url = 'https://openchat-review.me/assets/ogp.png';
    public string $site_name = 'LINE オープンチャットグラフ';
    public string $site_url = 'https://openchat-review.me';
    public string $locale = 'ja_JP';
    public string $og_type = 'website';
    public string $ld_type = 'Organization';

    public function setTitle(string $title)
    {
        $this->title = $this->h($title) . ' | ' . $this->title;
        return $this;
    }

    public function setDescription(string $description)
    {
        $this->description = $this->h($description);
        return $this;
    }

    public function setOgpDescription(string $ogpDescription)
    {
        $this->ogpDescription = $this->h($ogpDescription);
        return $this;
    }

    public function setImageUrl(string $image_url)
    {
        $this->image_url = $this->h($image_url);
        return $this;
    }

    public function generateTags(): string
    {
        $tags = '';
        $tags .= '<title>' . $this->title . '</title>' . "\n";
        $tags .= '<meta name="description" content="' . $this->description . '" />' . "\n";
        $tags .= '<meta property="og:description" content="' . $this->ogpDescription . '">' . "\n";
        $tags .= '<meta property="og:image" content="' . $this->image_url . '" />' . "\n";
        $tags .= '<meta property="og:site_name" content="' . $this->site_name . '" />' . "\n";
        $tags .= '<meta property="og:url" content="' . url($_SERVER['REQUEST_URI'] ?? '') . '" />' . "\n";
        $tags .= '<meta property="og:locale" content="' . $this->locale . '" />' . "\n";
        $tags .= '<meta property="og:type" content="' . $this->og_type . '" />' . "\n";
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
                        "target": "' . $this->site_url . '/{search_term_string}",
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
