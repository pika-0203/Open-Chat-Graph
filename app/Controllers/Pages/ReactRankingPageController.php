<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Services\StaticData\StaticDataFile;
use App\Views\Schema\PageBreadcrumbsListSchema;

class ReactRankingPageController
{
    function ranking(
        StaticDataFile $staticDataFile,
        PageBreadcrumbsListSchema $breadcrumbsShema,
        ?int $category
    ) {
        $_css = [
            'style/react/OpenChat.css',
            'style/react/OpenChatList.css',
            'style/react/SiteHeader.css',
            getFilePath('style/react', 'main.*.css')
        ];

        $_js = getFilePath('js/react', 'main.*.js');

        $categoryNames = array_flip(AppConfig::OPEN_CHAT_CATEGORY);
        if ($category && !isset($categoryNames[$category])) {
            return false;
        }

        $_meta = meta()
            ->setTitle($category ? ($categoryNames[$category] . '｜参加人数のランキング') : '【毎日更新】参加人数のランキング')
            ->generateTags();

        $_argDto = $staticDataFile->getRankingArgDto();

        $_breadcrumbsShema = $breadcrumbsShema->generateSchema('参加人数のランキング', 'ranking');

        return view('ranking_react_content', compact('_css', '_js', '_meta', '_argDto', '_breadcrumbsShema', 'category'));
    }
}
