<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;

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

        $_jsonData = file_get_contents(AppConfig::OPEN_CHAT_SUB_CATEGORIES_FILE_PATH);

        $rankingInfo = unserialize(file_get_contents(AppConfig::TOP_RANKING_INFO_FILE_PATH));
        $rankingUpdatedAt = $rankingInfo['rankingUpdatedAt'];

        return view('ranking_react_content', compact('_css', '_js', '_meta', '_jsonData', 'rankingUpdatedAt'));
    }
}
