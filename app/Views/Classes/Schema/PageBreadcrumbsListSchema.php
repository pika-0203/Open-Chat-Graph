<?php

namespace App\Views\Schema;

use App\Config\AppConfig;
use App\Views\Meta\Metadata;
use Spatie\SchemaOrg\DiscussionForumPosting;
use Spatie\SchemaOrg\Schema;

class PageBreadcrumbsListSchema
{
    const AuthorName = 'pika-0203(mimimiku778)(ずんだもん@オプチャグラフのバイト)';
    const AuthorUrl = ['https://github.com/pika-0203', 'https://github.com/mimimiku778', 'https://twitter.com/KTetrahydro'];
    const AuthorImage = 'https://avatars.githubusercontent.com/u/132340402?v=4';
    const PublisherName = 'オプチャグラフ';
    public string $publisherLogo;

    function __construct(
        private Metadata $metadata
    ) {
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

    function publisher()
    {
        $publisherName = self::PublisherName;
        $publisherLogo = $this->publisherLogo;
        return Schema::organization()
            ->name($publisherName)
            ->logo(
                Schema::imageObject()
                    ->url($publisherLogo)
            )
            ->explanationPage()
            ->url(url())
            ->description($this->metadata->description)
            ->email('support@openchat-review.me')
            ->sameAs(url('policy'));
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


        $webSite = Schema::webSite()
            ->headline($siteName)
            ->description($description)
            ->mainEntityOfPage(Schema::webPage()->id($url))
            ->image(Schema::imageObject()->url($image))
            ->author(Schema::person()->name($authorName)->image($authorImage)->sameAs($authorUrl))
            ->publisher($this->publisher())
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

        $webSite = Schema::webPage()
            ->headline($title)
            ->description($description)
            ->mainEntityOfPage(Schema::webPage()->id($url))
            ->image(Schema::imageObject()->url($image))
            ->author(Schema::person()->name($authorName)->image($authorImage)->sameAs($authorUrl))
            ->publisher($this->publisher())
            ->datePublished($datePublished)
            ->dateModified($dateModified);

        return $webSite->toScript();
    }

    private function room(array $room): DiscussionForumPosting
    {
        return Schema::discussionForumPosting()
            ->headline($room['name'])
            ->description($room['description'])
            ->url(url('oc/' . $room['id']))
            ->sameAs([AppConfig::LINE_OPEN_URL . $room['emid'], AppConfig::LINE_URL . $room['url']])
            ->numberOfParticipants($room['member'])
            ->image(imgPreviewUrl($room['id'], $room['img_url']))
            ->datePublished(new \DateTime($room['api_created_at'] ? '@' . $room['api_created_at'] : $room['created_at']))
            ->dateModified(new \DateTime($room['updated_at']))
            ->author(
                Schema::organization()
                    ->name('LINE (LY Corporation)')
                    ->url('https://line.me/ja/')
            );
    }

    function potentialAction()
    {
        return Schema::InteractAction()
            ->target(Schema::entryPoint()->urlTemplate(AppConfig::LINE_URL))
            ->name('参加')
            ->description('LINEアプリで参加する');
    }

    function actionApplication()
    {
        return Schema::softwareApplication()
            ->name('LINE')
            ->publisher(
                Schema::organization()
                    ->name('LINE (LY Corporation)')
                    ->url('https://line.me/ja/')
            )
            ->operatingSystem('iOS/Android/Windows/macOS')
            ->url('https://line.me/ja/')
            ->sameAs(['https://play.google.com/store/apps/details?id=jp.naver.line.android', 'https://apps.apple.com/jp/app/line/id443904275'])
            ->applicationCategory('https://www.wikidata.org/wiki/Q615985')
            ->applicationCategory('social software')
            ->genre('https://www.wikidata.org/wiki/Q2715623')
            ->genre('social networking')
            ->price(0)
            ->priceCurrency('JPY')
            ->eligibleRegion(
                Schema::country()
                    ->name('JP')
            )
            ->offers(
                Schema::offer()
                    ->name('LINEオープンチャット')
                    ->url('https://openchat.line.me/jp')
                    ->description('オープンチャットは、興味関心事や日常生活に密着した話題についてトークルームの中で会話や情報交換が楽しめます。ゲームの攻略情報、就活や恋愛の悩み相談、スポーツのリアルタイム応援などなど様々なテーマで毎日を楽しく！ ')
                    ->price(0)
                    ->priceCurrency('JPY')
            );
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

        // 各オープンチャットルームをItemListとして追加
        $itemList = Schema::itemList()
            ->potentialAction(
                $this->potentialAction()
            )
            ->actionApplication(
                $this->actionApplication()
            );

        $listArray = [];
        $rooms = array_slice($rooms, 0, 3);
        foreach ($rooms as $index => $room) {
            $listArray[] = Schema::listItem()
                ->name("「{$tag}」関連のおすすめオープンチャット")
                ->position($index + 1)
                ->item($this->room($room));
        }

        $itemList->itemListElement($listArray);

        // WebPageの構築
        $webSite = Schema::article()
            ->headline($title)
            ->description($description)
            ->mainEntityOfPage(Schema::collectionPage()->id($url))
            ->image(Schema::imageObject()->url($image))
            ->author(Schema::person()->name($authorName)->image($authorImage)->sameAs($authorUrl))
            ->publisher($this->publisher())
            ->datePublished($datePublished)
            ->dateModified($dateModified)
            ->articleSection(["「{$tag}」関連の人気おすすめオープンチャットを１時間毎の更新で紹介", 'カテゴリー:' . $tagCategory, '関連のタグ', ...array_slice($tags, 0, 5)])
            ->about(Schema::thing()->name($tag))
            ->mainEntity($itemList); // ItemListをhasPartプロパティを通じて追加

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
