<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\Recommend\RecommendGenarator;
use App\Services\Statistics\DownloadCsvService;
use App\Services\Statistics\StatisticsChartArrayService;
use App\Views\Dto\RankingPositionChartArgDto;
use App\Views\Meta\OcPageMeta;
use App\Views\Schema\OcPageSchema;
use App\Views\Schema\PageBreadcrumbsListSchema;
use App\Views\StatisticsViewUtility;
use Shadow\DB;

class OpenChatPageController
{
    function index(
        OpenChatPageRepositoryInterface $ocRepo,
        OcPageMeta $meta,
        StatisticsChartArrayService $statisticsChartArrayService,
        StatisticsViewUtility $statisticsViewUtility,
        PageBreadcrumbsListSchema $breadcrumbsShema,
        OcPageSchema $ocPageSchema,
        RecommendGenarator $recommendGenarator,
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

        $table = ['statistics_ranking_hour', 'statistics_ranking_day', 'statistics_ranking_week'];
        $order = ['ranking.id ASC', 'oc.member DESC'];
        $tableName = $table[array_rand($table)];
        $orderBy = $order[array_rand($order)];

        $recCategory = $_chartArgDto->categoryKey ? ('oc.category = ' . $_chartArgDto->categoryKey) : 1;

        $recommendTag = $recommendGenarator->geneTag($recommendGenarator->getRecommendTag($oc['id']) ?: '');
        $recommend = $recommendGenarator->getRecommend($oc['id']);

        $buldRecommendMaxNum = function ($i1, $i2, $i3) use (&$recommend, $oc, $recommendTag) {
            $recommend[$i1] = $recommend[$i2] ? max(array_column($recommend[$i2], 'member')) : 0;
            $recommend[$i1] = $recommend[$i1] > $oc['member'] ? $recommend[$i1] : ("「{$recommendTag}」関連" === $recommend[$i3] ? 0 : $recommend[$i1]);
        };

        $buldRecommendMaxNum(5, 0, 1);
        $buldRecommendMaxNum(6, 2, 3);

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
            '_schema',
            'recommend',
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

        $downloadCsvService->sendCsv($open_chat_id, $oc['category'] ?? 0, $oc['name']);
        exit;
    }
}
