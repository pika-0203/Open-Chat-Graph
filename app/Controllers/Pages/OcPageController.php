<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\Repositories\OpenChatRepositoryInterface;

class OcPageController
{
    function index(
        OpenChatRepositoryInterface $openChatRepository,
        \App\Services\Statistics\StatisticsService $statistics,
        int $open_chat_id
    ) {
        $oc = $openChatRepository->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        $statisticsData = $statistics->getStatisticsData($open_chat_id);

        $name = $oc['name'];
        $desc = "オープンチャット「{$name}」の人数推移をグラフで表示します。オプチャの人気度や活性度がチェック出来ます！";
        $ogpDesc = 'オプチャの人数推移をグラフで表示します。人気度や活性度がチェック出来ます！';

        $_meta = meta()->setTitle($name)->setDescription($desc)->setOgpDescription($ogpDesc);
        $_css = ['room_page_14', 'site_header_13', 'site_footer_7'];

        return view('statistics/oc_content', compact('_meta', '_css', 'oc', 'statisticsData'));
    }

    function csv(
        OpenChatRepositoryInterface $openChatRepository,
        \App\Services\Statistics\DownloadCsvService $csvService,
        int $open_chat_id
    ) {
        $oc = $openChatRepository->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        $csvService->sendCsv($open_chat_id, $oc['name']);
    }
}
