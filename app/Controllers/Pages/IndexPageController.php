<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use App\Services\User\MyOpenChatList;
use App\Services\StaticData\StaticDataGeneration;

class IndexPageController
{
    function index(StaticDataGeneration $staticDataGeneration)
    {
        $rankingList = $staticDataGeneration->getTopPageData();

        $myList = [];

        // クッキーにピン留めがある場合
        if (cookie()->has('myList')) {
            /** 
             * @var MyOpenChatList $myOpenChatList
             */
            $myOpenChatList = app(MyOpenChatList::class);
            $myList = $myOpenChatList->init() ? $myOpenChatList->get() : [];
        }

        $_css = ['room_list', 'site_header', 'site_footer'];
        $_meta = meta();
        $_meta->title = "{$_meta->title} | オープンチャットの人数統計とグラフ分析";

        $_updatedAt = OpenChatServicesUtility::getCronModifiedDate(new \DateTime('@' . $rankingList['updatedAt']))
            ->format('n/j');

        $_hourlyUpdatedAt = OpenChatServicesUtility::getModifiedCronTime($rankingList['hourlyUpdatedAt'])
            ->format('g:i');

        return view('top_content', compact('_meta', '_css', 'myList', '_updatedAt', '_hourlyUpdatedAt') + $rankingList);
    }
}
