<?php

namespace App\Views\Meta;

use Spatie\SchemaOrg\Schema;

class Metadata
{
    public string $title = 'オプチャグラフ';

    public string $description =
    'オプチャグラフは、オプチャの成長傾向を分析したり、匿名掲示板でディスカッションができる場所です。他のオプチャとの比較が簡単になり、トークルームの利用・運営に役立てることができます。';

    public string $ogpDescription =
    'オプチャグラフは、オプチャの成長傾向を分析したり、匿名掲示板でディスカッションができる場所です。他のオプチャとの比較が簡単になり、トークルームの利用・運営に役立てることができます。';

    public string $site_name = 'オプチャグラフ';
    public string $locale = 'ja_JP';
    public string $image_url;
    public string $site_url;
    public string $og_type;

    public function __construct()
    {
        if ($_SERVER["REQUEST_URI"] ?? '/' === '/') {
            $this->og_type = 'website';
        } else {
            $this->og_type = 'article';
        }

        $this->site_url = url();
        $this->image_url = url(\App\Config\AppConfig::DEFAULT_OGP_IMAGE_FILE_PATH);
    }

    public function setTitle(string $title): static
    {
        $this->title = h($title) . '｜' . $this->title;
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

    public function generateTags(): string
    {
        $tags = '';
        $tags .= '<title>' . $this->title . '</title>' . "\n";
        $tags .= '<meta name="description" content="' . $this->description . '">' . "\n";
        $tags .= '<meta property="og:locale" content="' . $this->locale . '">' . "\n";
        $tags .= '<meta property="og:url" content="' . rtrim(url(strstr(path(), '?', true) ?: path()), '/') . '">' . "\n";
        $tags .= '<meta property="og:type" content="' . $this->og_type . '">' . "\n";
        $tags .= '<meta property="og:title" content="' . $this->title . '">' . "\n";
        $tags .= '<meta property="og:description" content="' . $this->ogpDescription . '">' . "\n";
        $tags .= '<meta property="og:image" content="' . $this->image_url . '">' . "\n";
        $tags .= '<meta property="og:site_name" content="' . $this->site_name . '">' . "\n";
        $tags .= '<meta name="twitter:card"  content="summary">' . "\n";

        return $tags;
    }

    public function generateTopPageSchema(): string
    {
        return Schema::webSite()
            ->name($this->site_name)
            ->url($this->site_url)
            ->description($this->description)
            ->image($this->image_url)
            ->toScript();
    }

    public function __toString(): string
    {
        return $this->generateTags();
    }
}
