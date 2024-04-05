<?php

namespace App\Views\Schema;

use Spatie\SchemaOrg\Schema;

class PageBreadcrumbsListSchema
{
    function generateSchema(string $listItemName, string $path, string $secondName = '', string $secondPath = ''): string
    {
        // BreadcrumbListのインスタンスを作成
        $breadcrumbList = Schema::breadcrumbList();

        $itemListElement = [
            Schema::listItem()
                ->position(1)
                ->name('トップ')
                ->item(url()),
            Schema::listItem()
                ->position(2)
                ->name($listItemName)
                ->item(url($path)),
        ];

        if ($secondName && $secondPath) {
            $itemListElement[] = Schema::listItem()
                ->position(3)
                ->name($secondName)
                ->item(url($path . '/' . $secondPath));
        }

        // リストの要素を追加
        $breadcrumbList->itemListElement($itemListElement);

        // JSON-LDのマークアップを生成
        return $breadcrumbList->toScript();
    }

    function generateStructuredDataWebSite(
        string $siteName,
        string $description,
        string $url,
        string $image,
        string $authorName,
        string $authorUrl,
        string $authorImage,
        string $publisherName,
        string $publisherLogo,
        \DateTimeInterface $datePublished,
        \DateTimeInterface $dateModified
    ): string {
        $webSite = Schema::webSite()
            ->headline($siteName)
            ->description($description)
            ->mainEntityOfPage(Schema::webPage()->id($url))
            ->image(Schema::imageObject()->url($image))
            ->author(Schema::person()->name($authorName)->image($authorImage)->url($authorUrl))
            ->publisher(Schema::organization()->name($publisherName)->logo(Schema::imageObject()->url($publisherLogo)))
            ->datePublished($datePublished)
            ->dateModified($dateModified);

        return $webSite->toScript();
    }

    function generateStructuredDataWebPage(
        string $title,
        string $description,
        string $url,
        string $image,
        string $authorName,
        string $authorUrl,
        string $authorImage,
        string $publisherName,
        string $publisherLogo,
        \DateTimeInterface $datePublished,
        \DateTimeInterface $dateModified
    ): string {
        $webSite = Schema::webPage()
            ->headline($title)
            ->description($description)
            ->mainEntityOfPage(Schema::webPage()->id($url))
            ->image(Schema::imageObject()->url($image))
            ->author(Schema::person()->name($authorName)->image($authorImage)->url($authorUrl))
            ->publisher(Schema::organization()->name($publisherName)->logo(Schema::imageObject()->url($publisherLogo)))
            ->datePublished($datePublished)
            ->dateModified($dateModified);

        return $webSite->toScript();
    }
}
