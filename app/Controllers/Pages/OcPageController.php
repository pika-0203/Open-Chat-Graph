<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface;
use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Views\Meta\OcPageMeta;
use App\Services\Statistics\DownloadCsvService;
use App\Views\StatisticsViewUtility;

class OcPageController
{
    function index(
        StatisticsPageRepositoryInterface $statisticsRepo,
        OpenChatPageRepositoryInterface $ocRepo,
        OpenChatListRepositoryInterface $ocListRepo,
        OcPageMeta $meta,
        StatisticsViewUtility $statisticsViewUtility,
        int $open_chat_id
    ) {
        $oc = $ocRepo->getOpenChatById($open_chat_id);
        if (!$oc) {
            $redirectId = $ocRepo->getRedirectId($open_chat_id);

            return $redirectId ? redirect("oc/{$redirectId}", 301) : false;
        }

        $statisticsData = $statisticsRepo->getDailyStatisticsByPeriod($open_chat_id);


        $oc += $statisticsViewUtility->getOcPageArrayElementMemberDiff($statisticsData);


        $_css = ['site_header', 'site_footer', 'room_page'];

        $_meta = $meta->generateMetadata($open_chat_id, $oc);

        $myList = json_decode(cookie('myList') ?? '', true);
        if (!is_array($myList)) {
            $myList = [];
        }

        return view('oc_content', compact('_meta', '_css', 'oc', 'statisticsData', 'myList'));
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
