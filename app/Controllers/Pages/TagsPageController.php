<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Services\StaticData\StaticDataFile;
use App\Views\Schema\PageBreadcrumbsListSchema;

class TagsPageController
{
    function index(
        StaticDataFile $staticDataGeneration,
        PageBreadcrumbsListSchema $pageBreadcrumbsListSchema,
    ) {
        cache();

        $_css = ['room_list', 'site_header', 'site_footer'];
        $_meta = meta()->setTitle('タグ');

        $_schema = $pageBreadcrumbsListSchema->generateSchema('タグ', 'tags');
        $tagsGroup = $staticDataGeneration->getTagList();

        $categories = array_flip(AppConfig::OPEN_CHAT_CATEGORY);

        return view('tags_content', compact(
            '_meta',
            '_css',
            '_schema',
            'tagsGroup',
            'categories'
        ));
    }
}
