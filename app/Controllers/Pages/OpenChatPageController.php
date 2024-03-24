<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\Statistics\DownloadCsvService;
use App\Services\Statistics\StatisticsChartArrayService;
use App\Views\Dto\RankingPositionChartArgDto;
use App\Views\Meta\OcPageMeta;
use App\Views\Schema\OcPageSchema;
use App\Views\Schema\PageBreadcrumbsListSchema;
use App\Views\StatisticsViewUtility;

class OpenChatPageController
{
    function index(
        OpenChatPageRepositoryInterface $ocRepo,
        OcPageMeta $meta,
        StatisticsChartArrayService $statisticsChartArrayService,
        StatisticsViewUtility $statisticsViewUtility,
        PageBreadcrumbsListSchema $breadcrumbsShema,
        OcPageSchema $ocPageSchema,
        int $open_chat_id
    ) {
        $oc = $ocRepo->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        $_statsDto = $statisticsChartArrayService->buildStatisticsChartArray($open_chat_id);
        if (!$_statsDto) {
            throw new \RuntimeException('メンバー統計がありません');
        }

        $oc += $statisticsViewUtility->getOcPageArrayElementMemberDiff($_statsDto);

        $_css = ['site_header', 'site_footer', 'room_page', 'react/OpenChat', 'graph_page'];

        $_meta = $meta->generateMetadata($open_chat_id, $oc);

        $myList = json_decode(cookie('myList') ?? '', true);
        if (!is_array($myList)) {
            $myList = [];
        }

        $categoryValue = $oc['category'] ? array_search($oc['category'], AppConfig::OPEN_CHAT_CATEGORY) : null;

        $_chartArgDto = new RankingPositionChartArgDto;
        $_chartArgDto->id = $oc['id'];
        $_chartArgDto->categoryKey = $oc['category'] ?? (is_int($oc['api_created_at']) ? 0 : null);
        $_chartArgDto->categoryName = $categoryValue ?? 'すべて';
        $_chartArgDto->baseUrl = url();

        $category = $categoryValue ?? 'その他';

        $_commentArgDto = [
            'baseUrl' => url(),
            'openChatId' => $oc['id']
        ];

        $_breadcrumbsShema = $breadcrumbsShema->generateSchema('オープンチャット', 'oc');

        $_schema = $breadcrumbsShema->generateStructuredDataWebPage(
            $_meta->title,
            $_meta->description,
            url("oc/{$open_chat_id}"),
            url('assets/ogp.png'),
            'pika-0203',
            'https://github.com/pika-0203',
            'https://avatars.githubusercontent.com/u/132340402?v=4',
            'オプチャグラフ',
            url('assets/icon-192x192.png'),
            new \DateTime('@' . $oc['created_at']),
            new \DateTime($_statsDto->endDate),
        );

        $_ocPageSchema = $ocPageSchema->generateSchema(
            $oc['id'],
            $oc['name'],
            $oc['created_at'],
            strtotime($_statsDto->endDate)
        );

        return view('oc_content', compact(
            '_meta',
            '_css',
            'oc',
            'myList',
            'category',
            '_chartArgDto',
            '_statsDto',
            '_commentArgDto',
            '_breadcrumbsShema',
            '_ocPageSchema',
            '_schema'
        ));
    }

    function csv(
        OpenChatPageRepositoryInterface $ocRepo,
        DownloadCsvService $downloadCsvService,
        int $open_chat_id
    ) {
        $oc = $ocRepo->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        $downloadCsvService->sendCsv($open_chat_id, $oc['name']);
        exit;
    }
}
