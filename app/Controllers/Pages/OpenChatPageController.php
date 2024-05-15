<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Models\CommentRepositories\RecentCommentListRepositoryInterface;
use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\OpenChatAdmin\AdminOpenChat;
use App\Services\Recommend\OfficialPageList;
use App\Services\Recommend\RecommendGenarator;
use App\Services\StaticData\StaticDataFile;
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
        StaticDataFile $staticDataGeneration,
        RecentCommentListRepositoryInterface $recentCommentListRepository,
        int $open_chat_id,
        ?string $isAdmin,
    ) {
        $recommend = $recommendGenarator->getRecommend($open_chat_id);
        $oc = $ocRepo->getOpenChatById($open_chat_id);

        if (!$oc && !$recommend[2]) {
            return false;
        } elseif (!$oc) {
            $tag = $recommend[2];
            $_meta = meta()->setTitle("「{$tag}」タグ ID:{$open_chat_id} （オプチャグラフから削除済み）")
                ->setDescription("「{$tag}」タグ ID:{$open_chat_id} （オプチャグラフから削除済み）")
                ->setOgpDescription("「{$tag}」タグのオープンチャット ID:{$open_chat_id} （オプチャグラフから削除済み）");
            $_css = ['room_list', 'site_header', 'site_footer', 'recommend_list'];

            $_deleted = DB::fetch("SELECT * FROM open_chat_deleted WHERE id = :open_chat_id", compact('open_chat_id'));
            if (!$_deleted) return false;

            cacheControl(3600);
            return view('errors/oc_error', compact('_meta', '_css', 'recommend', 'open_chat_id', '_deleted'));
        }

        $_statsDto = $statisticsChartArrayService->buildStatisticsChartArray($open_chat_id);
        if (!$_statsDto) {
            throw new \RuntimeException('メンバー統計がありません');
        }

        $oc += $statisticsViewUtility->getOcPageArrayElementMemberDiff($_statsDto);

        $_css = ['room_list', 'site_header', 'site_footer', 'recommend_page', 'room_page', 'react/OpenChat', 'graph_page'];

        $_meta = $meta->generateMetadata($open_chat_id, $oc)
            ->setImageUrl(imgUrl($oc['id'], $oc['img_url']));

        $_meta->thumbnail = imgPreviewUrl($oc['id'], $oc['img_url']);

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

        if (isset($isAdmin)) {
            /** @var AdminOpenChat $admin */
            $admin = app(AdminOpenChat::class);
            $_adminDto = $admin->getDto($open_chat_id);
        } else {
            $_adminDto = null;
            cacheControl(300);
        }

        $dto = $staticDataGeneration->getTopPageData();

        $dto->hourlyList = array_slice($dto->hourlyList, 0, 5);
        $dto->dailyList = array_slice($dto->dailyList, 0, 5);

        if (($oc['emblem'] ?? 0) > 0) {
            /** @var OfficialPageList $admin */
            $officialPageList = app(OfficialPageList::class);
            $officialDto = $officialPageList->getListDto('1', 'スペシャルオープンチャット')[0];
            $officialDto2 = $officialPageList->getListDto('2', '公式認証オープンチャット')[0];
        } else {
            $officialDto = null;
            $officialDto2 = null;
        }

        return view('oc_content', compact(
            '_meta',
            '_css',
            'oc',
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
            'officialDto',
            'officialDto2',
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

        cacheControl(3600);
        $downloadCsvService->sendCsv($open_chat_id, $oc['name']);
        exit;
    }
}
