<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Recommend\RecommendGenarator;
use App\Views\Schema\PageBreadcrumbsListSchema;

class RecommendOpenChatPageController
{
    function __construct(
        private PageBreadcrumbsListSchema $breadcrumbsShema
    ) {
    }

    function index(RecommendGenarator $recommendGenarator, string $tag)
    {
        $recommend = $recommendGenarator->getRecomendRanking(0, $tag);
        if (!$recommend) {
            return false;
        }

        $count = $recommend->getCount();
        $pageTitle = "「{$tag}」のオープンチャットおすすめ{$count}選【最新】";
        $_css = ['room_list', 'site_header', 'site_footer'];

        $_meta = meta()->setTitle($pageTitle);

        $_breadcrumbsShema = $this->breadcrumbsShema->generateSchema('おすすめ', 'recommend', $tag, urlencode($tag));

        return view(
            'recommend_content',
            compact('_meta', '_css', '_breadcrumbsShema', 'recommend', 'tag', 'count')
        );
    }
}
