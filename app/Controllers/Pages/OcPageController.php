<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\Statistics\StatisticsService;
use App\Services\OcPageSchemaMarkup;

class OcPageController
{
    function index(
        OpenChatRepositoryInterface $openChatRepository,
        StatisticsService $statistics,
        OcPageSchemaMarkup $schemaMarkup,
        int $open_chat_id
    ) {
        $oc = $openChatRepository->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        $statisticsData = $statistics->getStatisticsData($open_chat_id);

        $diffMember = '';
        if ($oc['diff_member'] ?? 0 !== 0) {
            $diffNum = signedNum($oc['diff_member']);
            $diffPer = signedNum(singnedCeil($oc['percent_increase'] * 10) / 10);
            $diffMember = ", 前日比:{$diffNum}({$diffPer}%)";
        } elseif ($oc['diff_member'] === 0) {
            $diffMember = ', 前日比:±0';
        }

        $name = $oc['name'];
        $date = date('Y/m/d', $oc['updated_at']);
        $memberNum = $oc['member'];

        $desc = "オープンチャット「{$name}」の人数推移をグラフで表示します。オプチャの人気度や活性度がチェック出来ます！【{$date}】メンバー数:{$memberNum}{$diffMember}";

        $ogpDate = date('m/d', $oc['updated_at']);
        $ogpDesc = "オプチャの人数推移を分析。[{$ogpDate}]メンバー数:{$memberNum}{$diffMember}";

        $_meta = meta()->setTitle($name)->setDescription($desc)->setOgpDescription($ogpDesc);
        $_css = ['room_page_19', 'site_header_14', 'site_footer_10'];
        $_schema = $schemaMarkup->datasetSchemaMarkup($open_chat_id, $name, $oc['created_at'], $oc['updated_at']);

        return view('statistics/oc_content', compact('_meta', '_css', '_schema', 'oc', 'statisticsData'));
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
