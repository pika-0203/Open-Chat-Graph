<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Models\CommentRepositories\RecentCommentListRepositoryInterface;
use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\OpenChatAdmin\AdminOpenChat;
use App\Services\Recommend\RecommendGenarator;
use App\Services\StaticData\StaticDataFile;
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
        StaticDataFile $staticDataGeneration,
        RecentCommentListRepositoryInterface $recentCommentListRepository,
        int $open_chat_id,
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

        $_css = ['site_header', 'site_footer', 'room_page', 'react/OpenChat', 'graph_page', 'recommend_list', 'room_list'];

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

        $_breadcrumbsShema = $breadcrumbsShema->generateSchema(
            'オプチャ',
            'oc',
            $recommend[2] ? $recommend[2] : $category,
            (string)$open_chat_id
        );

        $updatedAt = new \DateTime($_statsDto->endDate);
        $_schema = $ocPageSchema->generateSchema(
            $_meta->title,
            $_meta->description,
            new \DateTime($oc['created_at']),
            $updatedAt,
            $recommend,
            $oc,
        );

        if (isset($oc['rh_diff_member']) && $oc['rh_diff_member'] >= AppConfig::MIN_MEMBER_DIFF_HOUR) {
            $hourlyUpdatedAt =  new \DateTime(file_get_contents(AppConfig::HOURLY_CRON_UPDATED_AT_DATETIME));

            $hourlyTime = $hourlyUpdatedAt->format(\DateTime::ATOM);
            $hourlyUpdatedAt->modify('-1hour');

            $_hourlyRange = '<time datetime="' . $hourlyTime . '">' . '1時間' . '</time>';
        } else {
            $_hourlyRange = null;
        }

        if (cookie()->has('admin') && cookie()->has('admin-enable')) {
            /** @var AdminOpenChat $admin */
            $admin = app(AdminOpenChat::class);
            $_adminDto = $admin->getDto($open_chat_id);
        } else {
            $_adminDto = null;
        }

        $dto = $staticDataGeneration->getTopPageData();
        $dto->recentCommentList = $recentCommentListRepository->findRecentCommentOpenChatAll(0, 15);

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
            '_hourlyRange',
            '_adminDto',
            'dto',
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
