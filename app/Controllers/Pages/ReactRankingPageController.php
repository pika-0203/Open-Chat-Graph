<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use App\Views\Dto\RankingArgDto;

class ReactRankingPageController
{
    function ranking()
    {
        $_css = [
            'style/react/OpenChat.css',
            'style/react/OpenChatList.css',
            'style/react/SiteHeader.css',
            getFilePath('style/react', 'main.*.css')
        ];

        $_js = getFilePath('js/react', 'main.*.js');

        $_meta = meta()
            ->setTitle('【毎日更新】参加人数のランキング')
            ->generateTags();

        $updatedAt = unserialize(file_get_contents(AppConfig::TOP_RANKING_INFO_FILE_PATH))['rankingUpdatedAt'];

        $_argDto = new RankingArgDto;
        $_argDto->baseUrl = url();
        $_argDto->rankingUpdatedAt = convertDatetime($updatedAt, true);
        $_argDto->modifiedUpdatedAtDate = OpenChatServicesUtility::getCronModifiedDate(new \DateTime('@' . $updatedAt))->format('Y-m-d');
        $_argDto->subCategories = json_decode(file_get_contents(AppConfig::OPEN_CHAT_SUB_CATEGORIES_FILE_PATH), true);

        return view('ranking_react_content', compact('_css', '_js', '_meta', '_argDto'));
    }
}
