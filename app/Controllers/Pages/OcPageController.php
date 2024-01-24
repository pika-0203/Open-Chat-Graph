<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface;
use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Views\Schema\OcPageSchema;
use App\Views\Meta\OcPageMeta;
use App\Services\Statistics\DownloadCsvService;
use App\Middleware\VerifyCsrfToken;
use App\Views\StatisticsViewUtility;

class OcPageController
{
    function index(
        StatisticsPageRepositoryInterface $statisticsRepo,
        OpenChatPageRepositoryInterface $ocRepo,
        OpenChatListRepositoryInterface $ocListRepo,
        OcPageSchema $schema,
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

        if ($oc['is_alive']) {
            $oc += $statisticsViewUtility->getOcPageArrayElementMemberDiff($statisticsData);
        } else {
            $oc += [
                'diff_member' => null,
                'percent_increase' => null,
                'diff_member2' => null,
                'percent_increase2' => null,
            ];
        }

        $_css = ['site_header', 'site_footer', 'room_page'];

        $archiveList = $ocListRepo->findArchives($open_chat_id);
        trimOpenChatListDescriptions($archiveList);
        if ($archiveList) {
            $_css[] = 'room_list';
        }

        $_meta = $meta->generateMetadata($open_chat_id, $oc);
        $_schema = $schema->generateSchema($open_chat_id, $oc['name'], $oc['created_at'], $oc['updated_at']);

        $myList = json_decode(cookie('myList') ?? '', true);
        if (!is_array($myList)) {
            $myList = [];
        }

        $noindex = false;
        if (isset($oc['is_alive']) && $oc['is_alive'] === 0) {
            $noindex = true;
        }

        return view('oc_content', compact('_meta', '_css', '_schema', 'oc', 'statisticsData', 'myList', 'archiveList', 'noindex'));
    }

    function archive(OpenChatPageRepositoryInterface $openChatRepository, int $open_chat_id, int $group_id)
    {
        $oc = $openChatRepository->getArciveById($open_chat_id, $group_id);
        if (!$oc) {
            return false;
        }

        $nextArchive = $openChatRepository->getNextArciveById($oc['archive_id'], $oc['id']);
        if (!$nextArchive) {
            $nextArchive = $openChatRepository->getOpenChatById($oc['id']);
        }

        if (!$nextArchive) {
            return false;
        }

        $updated = [
            'name' => $nextArchive['name'],
            'description' => $nextArchive['description']
        ];

        return view('archive_oc_content', compact('oc', 'updated'));
    }

    function csv(
        OpenChatPageRepositoryInterface $openChatRepository,
        DownloadCsvService $csvService,
        VerifyCsrfToken $token,
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
