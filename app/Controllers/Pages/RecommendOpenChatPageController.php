<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Recommend\RecommendGenarator;
use App\Services\StaticData\StaticDataFile;
use App\Views\Schema\PageBreadcrumbsListSchema;

class RecommendOpenChatPageController
{
    function __construct(
        private PageBreadcrumbsListSchema $breadcrumbsShema
    ) {
    }

    function index(RecommendGenarator $recommendGenarator, StaticDataFile $staticDataFile, string $tag)
    {
        $recommend = $recommendGenarator->getRecomendRanking(0, $tag);
        if (!$recommend) {
            return false;
        }

        $count = $recommend->getCount();
        $pageTitle = "「{$tag}」関連の人気オープンチャット{$count}選【最新】";
        $_css = ['room_list', 'site_header', 'site_footer'];

        $_meta = meta()->setTitle($pageTitle)->setDescription("最新の人気オープンチャットの中から、「{$tag}」にマッチ度が高いトークルームをご紹介！");

        $rankingDto = $staticDataFile->getRankingArgDto();

        $_updatedAt = new \DateTime($rankingDto->hourlyUpdatedAt);

        $_schema = $this->breadcrumbsShema->generateStructuredDataWebPage(
            $_meta->title,
            $_meta->description,
            url("recommend/" . urlencode($tag)),
            url('assets/ogp.png'),
            'pika-0203',
            'https://github.com/pika-0203',
            'https://avatars.githubusercontent.com/u/132340402?v=4',
            'オプチャグラフ',
            url('assets/icon-192x192.png'),
            new \DateTime('2024-04-06 08:00:00'),
            $_updatedAt,
        );

        $_breadcrumbsShema = $this->breadcrumbsShema->generateSchema('おすすめ', 'recommend', $tag, 'recommend?tag=' . urlencode($tag), true);

        $canonical = url('recommend?tag=' . $tag);

        return view(
            'recommend_content',
            compact('_meta', '_css', '_breadcrumbsShema', 'recommend', 'tag', 'count', '_schema', '_updatedAt', 'canonical')
        );
    }
}
