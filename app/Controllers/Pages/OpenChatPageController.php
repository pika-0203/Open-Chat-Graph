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
        $recommend = $recommendGenarator->getRecommend($open_chat_id);
        $oc = $ocRepo->getOpenChatById($open_chat_id);

        if (!$oc && !$recommend[2]) {
            return false;
        } elseif (!$oc) {
            $_meta = meta()->setTitle("削除されたオープンチャット")
                ->setDescription('お探しのオープンチャットは削除されました。')
                ->setOgpDescription('お探しのオープンチャットは削除されました。');
            $_css = ['room_list', 'site_header', 'site_footer', 'recommend_list'];
            http_response_code(404);
            return view('errors/oc_error', compact('_meta', '_css', 'recommend'));
        }

        $_statsDto = $statisticsChartArrayService->buildStatisticsChartArray($open_chat_id);
        if (!$_statsDto) {
            throw new \RuntimeException('メンバー統計がありません');
        }

        $oc += $statisticsViewUtility->getOcPageArrayElementMemberDiff($_statsDto);

        $_css = ['site_header', 'site_footer', 'room_page', 'react/OpenChat', 'graph_page', 'recommend_list'];

        $_meta = $meta->generateMetadata($open_chat_id, $oc)
            ->setImageUrl(imgUrl($oc['id'], $oc['img_url']));

        $_meta->thumbnail = imgUrl($oc['id'], $oc['img_url']);

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

        $_breadcrumbsShema = $breadcrumbsShema->generateSchema('オプチャ', 'oc', $oc['name'], (string)$open_chat_id);

        $updatedAt = new \DateTime($oc['updated_at']);
        $_schema = $ocPageSchema->generateSchema(
            $_meta->title,
            $_meta->description,
            new \DateTime($oc['created_at']),
            $updatedAt,
            $recommend,
            $oc,
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
            '_schema',
            'recommend',
            'updatedAt',
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
