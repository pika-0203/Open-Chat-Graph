<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Services\StaticData\StaticDataFile;
use App\Views\Schema\PageBreadcrumbsListSchema;
use Shadow\Kernel\Reception;

class ReactRankingPageController
{
    private function buildTitle(Reception $reception): string
    {
        $category = $reception->input('category');
        $keyword = $reception->input('keyword');
        $subCategory = $reception->input('sub_category');

        $title0 = '';
        switch (!!$keyword) {
            case true:
                $title0 = "「{$keyword}」の検索結果｜";
                break;
            default:
                $title0 = '';
        }

        $title1 = '';
        switch (!!$category) {
            case true:
                $title1 = array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] . '｜';
                break;
            default:
                $title1 = $title0 ? '' : '【毎日更新】';
        }

        $title3 = '';
        switch (!!$subCategory) {
            case true:
                $title3 = $subCategory . '｜';
                break;
            default:
                $title3 = '';
        }

        $title2 = '';
        switch ($reception->input('list')) {
            case 'weekly':
                $title2 = '人数増加・1週間';
                break;
            case 'daily':
                $title2 = '人数増加・24時間';
                break;
            case 'hourly':
                $title2 = '人数増加・1時間';
                break;
            default:
                $title2 = '参加人数のランキング';
        }

        return $title0 . $title1 . $title3 . $title2;
    }

    function ranking(
        StaticDataFile $staticDataFile,
        PageBreadcrumbsListSchema $breadcrumbsShema,
        Reception $reception,
        int $category
    ) {
        $_css = [
            'style/react/OpenChat.css',
            'style/react/OpenChatList.css',
            'style/react/SiteHeader.css',
            getFilePath('style/react', 'main.*.css')
        ];

        $_js = getFilePath('js/react', 'main.*.js');

        $_meta = meta()
            ->setTitle($this->buildTitle($reception))
            ->generateTags();

        $_argDto = $staticDataFile->getRankingArgDto();

        $_breadcrumbsShema = $breadcrumbsShema->generateSchema(
            'ランキング',
            'ranking',
            $category ? array_flip(AppConfig::OPEN_CHAT_CATEGORY)[$category] : '',
            $category ? (string)$category : ''
        );

        return view('ranking_react_content', compact('_css', '_js', '_meta', '_argDto', '_breadcrumbsShema', 'category'));
    }
}
