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
        $_meta = meta()->setTitle('タグからオプチャを探す')->setDescription('各タグを、最も近いカテゴリに分類して表示しています。タグ内のルーム自体は、様々なカテゴリに属しています。');

        $_schema = $pageBreadcrumbsListSchema->generateSchema('タグ', 'tags');

        $tagsGroup = (function ($tagsGroup) {
            $exists = [];
            return array_map(function ($tags) use (&$exists) {
                $result = [];
                foreach ($tags as $tag) {
                    if (in_array($tag['tag'], $exists)) continue;
                    $exists[] = $tag['tag'];
                    $result[] = $tag;
                }

                return $result;
            }, $tagsGroup);
        })($staticDataGeneration->getTagList());

        $categories = array_flip(AppConfig::OPEN_CHAT_CATEGORY);
        $_updatedAt = new \DateTime(file_get_contents(AppConfig::HOURLY_REAL_UPDATED_AT_DATETIME));

        return view('tags_content', compact(
            '_meta',
            '_css',
            '_schema',
            'tagsGroup',
            'categories',
            '_updatedAt'
        ));
    }
}
