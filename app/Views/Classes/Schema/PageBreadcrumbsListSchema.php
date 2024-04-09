<?php

namespace App\Views\Schema;

use Spatie\SchemaOrg\Schema;

class PageBreadcrumbsListSchema
{
    const AuthorName = 'pika-0203(mimimiku778)(ずんだもん@オプチャグラフのバイト)';
    const AuthorUrl = ['https://github.com/pika-0203', 'https://github.com/mimimiku778', 'https://twitter.com/KTetrahydro'];
    const AuthorImage = 'https://avatars.githubusercontent.com/u/132340402?v=4';
    const PublisherName = 'オプチャグラフ';
    public string $publisherLogo;

    function __construct()
    {
        $this->publisherLogo = url('assets/icon-192x192.png');
    }

    function generateSchema(string $listItemName, string $path, string $secondName = '', string $secondPath = '', bool $fullPath = false): string
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
                ->item(url($fullPath ? $secondPath : ($path . '/' . $secondPath)));
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
        \DateTimeInterface $datePublished,
        \DateTimeInterface $dateModified
    ): string {
        $authorName = self::AuthorName;
        $authorUrl = self::AuthorUrl;
        $authorImage = self::AuthorImage;
        $publisherName = self::PublisherName;
        $publisherLogo = $this->publisherLogo;

        $webSite = Schema::webSite()
            ->headline($siteName)
            ->description($description)
            ->mainEntityOfPage(Schema::webPage()->id($url))
            ->image(Schema::imageObject()->url($image))
            ->author(Schema::person()->name($authorName)->image($authorImage)->sameAs($authorUrl))
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
        \DateTimeInterface $datePublished,
        \DateTimeInterface $dateModified
    ): string {
        $authorName = self::AuthorName;
        $authorUrl = self::AuthorUrl;
        $authorImage = self::AuthorImage;
        $publisherName = self::PublisherName;
        $publisherLogo = $this->publisherLogo;

        $webSite = Schema::webPage()
            ->headline($title)
            ->description($description)
            ->mainEntityOfPage(Schema::webPage()->id($url))
            ->image(Schema::imageObject()->url($image))
            ->author(Schema::person()->name($authorName)->image($authorImage)->sameAs($authorUrl))
            ->publisher(Schema::organization()->name($publisherName)->logo(Schema::imageObject()->url($publisherLogo)))
            ->datePublished($datePublished)
            ->dateModified($dateModified);

        return $webSite->toScript();
    }

    function generateRecommend(
        string $title,
        string $description,
        string $url,
        string $image,
        \DateTimeInterface $datePublished,
        \DateTimeInterface $dateModified,
        string $tag,
        array $tags,
        string $tagCategory,
        array $rooms // オープンチャットルームの情報を配列で追加
    ): string {
        $authorName = self::AuthorName;
        $authorUrl = self::AuthorUrl;
        $authorImage = self::AuthorImage;
        $publisherName = self::PublisherName;
        $publisherLogo = $this->publisherLogo;

        // 各オープンチャットルームをItemListとして追加
        $itemList = Schema::itemList();
        $listArray = [];
        foreach ($rooms as $index => $room) {
            $listArray[] = Schema::listItem()
                ->position($index + 1)
                ->item(
                    Schema::webPage()
                        ->name($room['name'])
                        ->description($room['description'])
                        ->url(url('oc/' . $room['id']))
                );
        }

        $itemList->itemListElement($listArray);

        // WebPageの構築
        $webSite = Schema::article()
            ->headline($title)
            ->description($description)
            ->mainEntityOfPage(Schema::collectionPage()->id($url))
            ->image(Schema::imageObject()->url($image))
            ->author(Schema::person()->name($authorName)->image($authorImage)->sameAs($authorUrl))
            ->publisher(Schema::organization()->name($publisherName)->logo(Schema::imageObject()->url($publisherLogo)))
            ->dateModified($dateModified)
            ->articleSection([$tagCategory, ...array_slice($tags, 0, 5)])
            ->about(Schema::thing()->name($tag))
            ->mainEntity($itemList); // ItemListをhasPartプロパティを通じて追加

        return $webSite->toScript();
    }
}
