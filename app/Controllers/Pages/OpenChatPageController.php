<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\Statistics\StatisticsChartArrayService;
use App\Views\Dto\RankingPositionChartArgDto;
use App\Views\Meta\OcPageMeta;
use App\Views\StatisticsViewUtility;

class OpenChatPageController
{
    function index(
        OpenChatPageRepositoryInterface $ocRepo,
        OcPageMeta $meta,
        StatisticsChartArrayService $statisticsChartArrayService,
        StatisticsViewUtility $statisticsViewUtility,
        int $open_chat_id
    ) {
        $oc = $ocRepo->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        $_statsDto = $statisticsChartArrayService->buildStatisticsChartArray($open_chat_id);
        $oc += $statisticsViewUtility->getOcPageArrayElementMemberDiff($_statsDto);

        $_css = ['site_header', 'site_footer', 'room_page', 'react/OpenChat', 'graph_page'];

        $_meta = $meta->generateMetadata($open_chat_id, $oc);

        $myList = json_decode(cookie('myList') ?? '', true);
        if (!is_array($myList)) {
            $myList = [];
        }

        $category = $oc['category'] ? array_search($oc['category'], AppConfig::OPEN_CHAT_CATEGORY) : 'その他';

        $_chartArgDto = new RankingPositionChartArgDto;
        $_chartArgDto->id = $oc['id'];
        $_chartArgDto->categoryKey = $oc['category'] ?? 0;
        $_chartArgDto->categoryName = $category;
        $_chartArgDto->baseUrl = url();

        return view('oc_content', compact('_meta', '_css', 'oc', 'myList', 'category', '_chartArgDto', '_statsDto'));
    }
}
