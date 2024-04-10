<?php

namespace App\Views\Schema;

use App\Config\AppConfig;
use App\Config\OpenChatCrawlerConfig;
use App\Views\Meta\Metadata;
use Spatie\SchemaOrg\DiscussionForumPosting;
use Spatie\SchemaOrg\Schema;

class PageBreadcrumbsListSchema
{
    const AuthorName = 'pika-0203';
    const AuthorUrl = ['https://github.com/mimimiku778', 'https://twitter.com/KTetrahydro'];
    const AuthorImage = ['https://avatars.githubusercontent.com/u/132340402?v=4', 'https://avatars.githubusercontent.com/u/116529486?v=4', 'https://pbs.twimg.com/profile_images/1767178994347397120/9u-TS_lj_400x400.jpg'];
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
            ->explanationPage()
            ->url(rtrim(url(), '/'))
            ->description($this->metadata->description)
            ->email('support@openchat-review.me')
            ->sameAs(url('policy'));
    }

    function person()
    {
        $authorName = self::AuthorName;
        $authorUrl = self::AuthorUrl;
        $authorImage = self::AuthorImage;
        return Schema::person()
            ->name($authorName)
            ->image($authorImage)
            ->jobTitle('オプチャグラフの開発者')
            ->affiliation('オプチャグラフ')
            ->url('https://github.com/pika-0203')
            ->sameAs($authorUrl);
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
            ->publisher($this->publisher())
            ->author($this->person())
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
            ->url('https://openchat.line.me/jp')
            ->sameAs([
                'https://openchat-jp.line.me/',
                'https://twitter.com/LINEOpenChat_JP',
                'https://www.facebook.com/people/LINE-OpenChat-Japan/100030725126645/',
                'https://www.youtube.com/channel/UCCH9pcP4VK4OIY1bId-KXhw',
            ])
            ->logo('https://openchat.line.me/og_tag_default_image.png');
    }

    function lineOrganization()
    {
        return Schema::organization()
            ->name('LINE (LY Corporation)')
            ->url('https://line.me/ja/')
            ->sameAs([
                'https://twitter.com/LINEjp_official',
                'https://www.youtube.com/@LINE_jp',
                'https://www.facebook.com/jpn.LINE'
            ])
            ->logo('https://line.me/static/a83a28aa13ec25daa7b25a9d20e55d66/aca38/og.png');
    }


    function room(array $room): DiscussionForumPosting
    {
        return Schema::discussionForumPosting()
            ->headline($room['name'])
            ->description($room['description'])
            ->url(url('oc/' . $room['id']))
            ->sameAs([
                AppConfig::LINE_OPEN_URL . $room['emid'] . AppConfig::LINE_OPEN_URL_SUFFIX,
                $room['url'] ? AppConfig::LINE_APP_URL . $room['url'] . AppConfig::LINE_APP_SUFFIX : ''
            ])
            ->interactionStatistic(
                Schema::interactionCounter()
                    ->interactionType('https://schema.org/FollowAction')
                    ->userInteractionCount($room['member'])
            )
            ->image([
                imgUrl($room['id'], $room['img_url']),
                imgPreviewUrl($room['id'], $room['img_url']),
                OpenChatCrawlerConfig::LINE_IMG_URL . $room['api_img_url'],
                OpenChatCrawlerConfig::LINE_IMG_URL . $room['api_img_url'] . '/preview',
                OpenChatCrawlerConfig::LINE_IMG_URL . $room['api_img_url'] . '/preview.100x100',
            ])
            ->datePublished(new \DateTime($room['api_created_at'] ? '@' . $room['api_created_at'] : $room['created_at']))
            ->dateModified(new \DateTime($room['updated_at']))
            ->provider(
                $this->lineOcOrganization()
            )
            ->author(
                Schema::person()
                    ->name('匿名ユーザー')
                    ->url(AppConfig::LINE_OPEN_URL . $room['emid'] . AppConfig::LINE_OPEN_URL_SUFFIX)
                    ->image([
                        OpenChatCrawlerConfig::LINE_IMG_URL . $room['api_img_url'],
                        OpenChatCrawlerConfig::LINE_IMG_URL . $room['api_img_url'] . '/preview',
                        OpenChatCrawlerConfig::LINE_IMG_URL . $room['api_img_url'] . '/preview.100x100',
                    ])
            );
    }

    function actionApplication()
    {
        return Schema::softwareApplication()
            ->name('LINE')
            ->publisher(
                $this->lineOrganization()
            )
            ->operatingSystem('iOS/Android/Windows/macOS')
            ->url('https://line.me/download')
            ->sameAs(
                [
                    'https://apps.apple.com/jp/app/line/id443904275',
                    'https://play.google.com/store/apps/details?id=jp.naver.line.android',
                    'https://line-android-universal-download.line-scdn.net/line-apk-download.html',
                    'https://apps.microsoft.com/store/detail/line-desktop/XPFCC4CD725961',
                    'https://apps.apple.com/jp/app/line/id539883307?mt=12',
                ]
            )
            ->applicationCategory('https://www.wikidata.org/wiki/Q615985')
            ->genre('https://www.wikidata.org/wiki/Q2715623');
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
            ->name('LINEで開く')
            ->description('LINEアプリでオープンチャットに参加する')
            ->agent(
                $this->lineOcOrganization()
            );
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
        $itemList = Schema::itemList()
            ->name($title)
            ->description($description);

        $listArray = [];
        foreach ($rooms as $index => $room) {
            $listArray[] = Schema::listItem()
                ->position($index + 1)
                ->item($this->room($room));
        }

        $itemList->itemListElement($listArray);

        // WebPageの構築
        $webSite = Schema::article()
            ->headline($title)
            ->description($description)
            ->image([
                imgUrl($rooms[0]['id'], $rooms[0]['img_url']),
                imgPreviewUrl($rooms[0]['id'], $rooms[0]['img_url']),
            ])
            ->publisher($this->publisher())
            ->author($this->person())
            ->datePublished($datePublished)
            ->dateModified($dateModified)
            ->articleSection(["「{$tag}」関連のおすすめ人気オプチャ{$count}選【最新】", '関連性が高いタグ', "「{$tag}」関連のおすすめ {$count}件", "メンバー数のアイコンについて（おすすめ基準）"])
            ->about(Schema::thing()->name($tag))
            ->mainEntityOfPage(
                Schema::collectionPage()
                    ->id($url)
                    ->offers(
                        Schema::offer()
                            ->potentialAction($this->potentialAction())
                    )
            )->mainEntity($itemList);

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
