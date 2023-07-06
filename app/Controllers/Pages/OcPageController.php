<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\Statistics\StatisticsService;
use App\Views\Schema\OcPageSchema;
use App\Views\Meta\OcPageMeta;

class OcPageController
{
    function index(
        OpenChatRepositoryInterface $openChatRepository,
        StatisticsService $statistics,
        OcPageSchema $schema,
        OcPageMeta $meta,
        int $open_chat_id
    ) {
        $oc = $openChatRepository->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        $statisticsData = $statistics->getStatisticsData($open_chat_id);

        $_meta = $meta->generateMetadata($oc);
        $_css = ['site_header_21', 'site_footer_18', 'room_page_28'];
        $_schema = $schema->generateSchema($open_chat_id, $oc['name'], $oc['created_at'], $oc['updated_at']);

        return view('statistics/oc_content', compact('_meta', '_css', '_schema', 'oc', 'statisticsData'));
    }

    function csv(
        OpenChatRepositoryInterface $openChatRepository,
        \App\Services\Statistics\DownloadCsvService $csvService,
        \App\Middleware\VerifyCsrfToken $token,
        int $open_chat_id
    ) {
        $token->verifyCsrfToken();
        
        $oc = $openChatRepository->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        $csvService->sendCsv($open_chat_id, $oc['name']);
    }
}
