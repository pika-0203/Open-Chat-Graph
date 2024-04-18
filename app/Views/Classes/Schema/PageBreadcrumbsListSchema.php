<?php

namespace App\Views\Schema;

use App\Config\AppConfig;
use App\Views\Meta\Metadata;
use Spatie\SchemaOrg\DiscussionForumPosting;
use Spatie\SchemaOrg\Schema;

class PageBreadcrumbsListSchema
{
    const AuthorName = 'pika-0203';
    const AuthorUrl = 'https://github.com/pika-0203';
    const PublisherName = 'オプチャグラフ';
    public string $publisherLogo;
    public string $siteImg;

    function __construct(
        private Metadata $metadata
    ) {
        $this->publisherLogo = url('assets/icon-192x192.png');
        $this->siteImg = url('assets/ogp.png');
    }

    function generateSchema(string $listItemName, string $path, string $secondName = '', string $secondPath = '', bool $fullPath = false): string
    {
        // BreadcrumbListのインスタンスを作成
        $breadcrumbList = Schema::breadcrumbList();

        $itemListElement = [
            Schema::listItem()
                ->position(1)
                ->name('トップ')
                ->item(rtrim(url(), '/')),
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

    function publisher()
    {
        $publisherName = self::PublisherName;
        $publisherLogo = $this->publisherLogo;
        return Schema::organization()
            ->name($publisherName)
            ->logo($publisherLogo)
            ->description($this->metadata->description)
            ->email('support@openchat-review.me')
            ->url(url())
            ->sameAs(url('policy'));
    }

    function person()
    {
        $authorName = self::AuthorName;
        $authorUrl = self::AuthorUrl;
        return Schema::person()
            ->name($authorName)
            ->url($authorUrl);
    }

    function generateStructuredDataWebSite(
        string $siteName,
        string $description,
        string $url,
        string $image,
        \DateTimeInterface $datePublished,
        \DateTimeInterface $dateModified
    ): string {
        $webSite = Schema::webSite()
            ->name($siteName)
            ->description($description)
            ->image($image)
            ->author($this->publisher())
            ->url($url)
            ->datePublished($datePublished)
            ->dateModified($dateModified)
            ->potentialAction(
                Schema::searchAction()
                    ->target(
                        Schema::entryPoint()
                            ->urlTemplate(url('ranking?keyword={search_term_string}'))
                    )
                    ->{'query-input'}('required name=search_term_string')
            );

        return $webSite->toScript();
    }

    function lineOcOrganization()
    {
        return Schema::organization()
            ->name('LINEオープンチャット')
            ->alternateName('オプチャ')
            ->url('https://openchat-jp.line.me/other/beginners_guide');
    }

    function lineOrganization()
    {
        return Schema::organization()
            ->name('LINE');
    }

    function room(array $room): DiscussionForumPosting
    {
        return Schema::discussionForumPosting()
            ->headline($room['name'])
            ->description($room['description'])
            ->url(AppConfig::LINE_OPEN_URL . $room['emid'] . AppConfig::LINE_OPEN_URL_SUFFIX)
            ->sameAs($room['url'] ? AppConfig::LINE_APP_URL . $room['url'] . AppConfig::LINE_APP_SUFFIX : '')
            ->interactionStatistic(
                Schema::interactionCounter()
                    ->interactionType('https://schema.org/FollowAction')
                    ->userInteractionCount($room['member'])
            )
            ->image([
                imgUrl($room['id'], $room['img_url']),
            ])
            ->datePublished(new \DateTime($room['created_at']))
            ->dateModified(new \DateTime($room['updated_at']))
            ->provider(
                $this->lineOcOrganization()
            )
            ->author(
                Schema::person()
                    ->name($room['name'])
                    ->url(AppConfig::LINE_OPEN_URL . $room['emid'] . AppConfig::LINE_OPEN_URL_SUFFIX)
            );
    }

    function actionApplication()
    {
        return Schema::softwareApplication()
            ->name('LINE')
            ->url('https://line.me/download')
            ->operatingSystem('iOS/Android/Windows/macOS')
            ->applicationCategory('https://www.wikidata.org/wiki/Q615985');
    }

    function potentialAction()
    {
        return Schema::InteractAction()
            ->target(
                Schema::entryPoint()
                    ->urlTemplate(AppConfig::LINE_APP_URL . '{invitationTicket}' . AppConfig::LINE_APP_SUFFIX)
                    ->actionApplication($this->actionApplication())
            )
            ->additionalType('https://schema.org/FollowAction')
            ->name('LINEで開く');
    }

    function generateRecommend(
        string $title,
        string $description,
        string $url,
        \DateTimeInterface $datePublished,
        \DateTimeInterface $dateModified,
        string $tag,
        array $rooms // オープンチャットルームの情報を配列で追加
    ): string {
        $count = count($rooms);
        // 各オープンチャットルームをItemListとして追加
        $itemList = Schema::itemList();

        $listArray = [];
        foreach ($rooms as $index => $room) {
            $listArray[] = Schema::listItem()
                ->item(
                    schema::article()
                        ->headline($room['name'])
                        ->description($room['description'])
                        ->image(imgPreviewUrl($room['id'], $room['img_url']))
                        ->url(url('oc/' . $room['id']))
                        ->position($index + 1)
                        ->author($this->person())
                );
        }

        $itemList->itemListElement($listArray);

        // WebPageの構築
        $webSite = Schema::article()
            ->headline($title)
            ->description($description)
            ->image(imgUrl($rooms[0]['id'], $rooms[0]['img_url']))
            ->publisher($this->publisher())
            ->author($this->person())
            ->datePublished($datePublished)
            ->dateModified($dateModified)
            ->articleSection([$title, '関連のキーワード', "「{$tag}」のランキング {$count}件", "メンバー数のアイコンについて", "はじめてのLINEオープンチャットガイド（LINE公式）"])
            ->about(Schema::thing()->name($tag))
            ->mainEntityOfPage(
                Schema::collectionPage()
                    ->id($url)
            )
            ->mainEntity($itemList);

        return $webSite->toScript();
    }

    function searchAction()
    {
        return Schema::searchAction()
            ->target(
                Schema::entryPoint()
                    ->urlTemplate(url('ranking?keyword={search_term_string}'))
            )
            ->query('equired name=search_term_string');
    }
}
