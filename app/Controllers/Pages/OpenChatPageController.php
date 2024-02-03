<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface;
use App\Views\Meta\OcPageMeta;
use App\Services\Statistics\DownloadCsvService;
use App\Views\StatisticsViewUtility;

class OpenChatPageController
{
    function index(
        OpenChatPageRepositoryInterface $ocRepo,
        OcPageMeta $meta,
        StatisticsViewUtility $statisticsViewUtility,
        int $open_chat_id
    ) {
        $oc = $ocRepo->getOpenChatById($open_chat_id);
        if (!$oc) {
            $redirectId = $ocRepo->getRedirectId($open_chat_id);

            return $redirectId ? redirect("oc/{$redirectId}", 301) : false;
        }

        if (excludeTime()) {
            $rankingInfo = unserialize(file_get_contents(AppConfig::TOP_RANKING_INFO_FILE_PATH));
            $isUpdated = date('Y-m-d', $rankingInfo['rankingUpdatedAt']) === date('Y-m-d');
            if (!$isUpdated) {
                /** @var StatisticsPageRepositoryInterface $statisticsRepo */
                $statisticsRepo = app(StatisticsPageRepositoryInterface::class);
                $statisticsData = $statisticsRepo->getDailyStatisticsByPeriod($open_chat_id);
                $oc += $statisticsViewUtility->getOcPageArrayElementMemberDiff($statisticsData);
            }
        }

        $_css = ['site_header', 'site_footer', 'room_page', 'react/OpenChat', 'graph_page'];

        $_meta = $meta->generateMetadata($open_chat_id, $oc);

        $myList = json_decode(cookie('myList') ?? '', true);
        if (!is_array($myList)) {
            $myList = [];
        }

        $category = $oc['category'] ? array_search($oc['category'], AppConfig::OPEN_CHAT_CATEGORY) : '';

        return view('oc_content', compact('_meta', '_css', 'oc', 'myList', 'category'));
    }

    function csv(
        OpenChatPageRepositoryInterface $openChatRepository,
        DownloadCsvService $csvService,
        int $open_chat_id
    ) {
        $oc = $openChatRepository->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        $csvService->sendCsv($open_chat_id, $oc['name']);
    }
}
