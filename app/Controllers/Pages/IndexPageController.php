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

        $updatedAtTime = OpenChatServicesUtility::getCronModifiedDate(new \DateTime('@' . $rankingList['updatedAt']));
        $_dailyStart = $updatedAtTime->format('n月j日');

        $updatedAtTime->modify('-7day');
        $weeklyStart = $updatedAtTime->format('n月j日');
        $_weeklyRange = $weeklyStart . ' 〜 ' . $_dailyStart;

        $hourlyUpdatedAt = OpenChatServicesUtility::getModifiedCronTime($rankingList['hourlyUpdatedAt']);

        $hourlyEnd = $hourlyUpdatedAt->format('G:i');
        $hourlyUpdatedAt->modify('-1hour');
        $hourlyStart = $hourlyUpdatedAt->format('G:i');
        $_hourlyRange = $hourlyStart . ' 〜 ' . $hourlyEnd;

        return view('top_content', compact('_meta', '_css', 'myList', '_dailyStart', '_hourlyRange', '_weeklyRange') + $rankingList);
    }
}
