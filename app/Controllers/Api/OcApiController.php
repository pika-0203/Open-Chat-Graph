<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Config\AppConfig;
use App\Services\OpenChat\Registration\OpenChatFromCrawlerRegistration;
use App\Models\GCE\GceDbRecordSynchronizer;
use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\CronJson\RankingPositionHourUpdaterState;
use App\Services\RankingPosition\RankingPositionHourApiService;

class OcApiController
{
    /**
     * {
     *  id: number,
     *  name: string,
     *  url: string,
     *  img_url: string,
     *  description: string,
     *  member: number,
     *  api_created_at: number | null,  //ランキング未掲載の場合null
     *  emblem: 0 | 1 | 2 | null,       //ランキング未掲載の場合null
     *  category: number | null,        //ランキング未掲載の場合null
     *  emid: string | null,            //ランキング未掲載の場合null
     *  created_at: number | null,      //ランキング未掲載の場合null
     *  updated_at: number | null,      //ランキング未掲載の場合null
     *  is_alive: 1 | 0,                //削除済みオプチャは0
     * }
     */
    function json(
        OpenChatPageRepositoryInterface $ocRepo,
        int $open_chat_id
    ) {
        $oc = $ocRepo->getOpenChatById($open_chat_id);
        if (!$oc) {
            return false;
        }

        return response($oc);
    }

    /**
     * {
     *  name: string;
     *  next_update: string | undifined;                  // ISO 8601
     *  category: string | undifined;                     // ランキング未掲載の場合 undifined
     *  updated_at: string | undifined;                   // ISO 8601 ランキング未掲載・更新中の場合 undifined
     *  rising_position: number | false | undifined;      // ランキング未掲載・更新中の場合 undifined
     *  rising_total_count: number | undifined;           // ランキング未掲載・更新中の場合 undifined
     *  rising_all_position: number | false | undifined;  // ランキング未掲載・更新中の場合 undifined
     *  rising_all_total_count: number| undifined;        // ランキング未掲載・更新中の場合 undifined
     *  ranking_position: number | false | undifined;     // ランキング未掲載・更新中の場合 undifined
     *  ranking_total_count: number | undifined;          // ランキング未掲載・更新中の場合 undifined
     *  ranking_all_position: number | false | undifined; // ランキング未掲載・更新中の場合 undifined
     *  ranking_all_total_count: number | undifined;      // ランキング未掲載・更新中の場合 undifined
     *  member: number | false | undifined;               // ランキング未掲載・更新中の場合 undifined
     * }
     */
    function officialRankingPosition(
        OpenChatPageRepositoryInterface $ocRepo,
        RankingPositionHourApiService $service,
        RankingPositionHourUpdaterState $state,
        int $open_chat_id
    ) {
        $oc = $ocRepo->getOpenChatById($open_chat_id);
        if (!$oc || !$oc['is_alive']) {
            return false;
        }

        [
            'emid' => $emid,
            'name' => $name,
            'category' => $categoryIndex,
        ] = $oc;

        $next_update = $service->getNextUpdate()->format(\DateTime::ATOM);

        if (!$emid) {
            // ランキング未掲載でカテゴリがない場合
            return response(compact('name', 'next_update'));
        }

        $categoryName = AppConfig::OPEN_CHAT_CATEGORY_KEYS[$categoryIndex];

        $dto = $service->getLatestRanking($emid, $categoryIndex);
        if (!$dto || $state->isActive) {
            // ランキング未更新・更新中の場合
            $next_update = $service->getTentativeNextUpdate()->format(\DateTime::ATOM);
            return response(compact('name', 'category', 'next_update'));
        }

        $updated_at = $service->getCurrentTime()->format(\DateTime::ATOM);

        return response([
            'name' => $name,
            'next_update' => $next_update,
            'category' => $categoryName,
            'updated_at' => $updated_at,
            'rising_position' => $dto->rising_position,
            'rising_total_count' => $dto->rising_total_count,
            'rising_all_position' => $dto->rising_all_position,
            'rising_all_total_count' => $dto->rising_all_total_count,
            'member' => $dto->member,
            'ranking_position' => $dto->ranking_position,
            'ranking_total_count' => $dto->ranking_total_count,
            'ranking_all_position' => $dto->ranking_all_position,
            'ranking_all_total_count' => $dto->ranking_all_total_count
        ]);
    }

    function post(OpenChatFromCrawlerRegistration $openChat, GceDbRecordSynchronizer $gce, string $url)
    {
        $openChat->getNumAddOpenChatPerMinute();
        $result = $openChat->registerOpenChatFromCrawler(sanitizeString($url));

        if ($result['message'] === 'オープンチャットを登録しました') {
            $gce->syncOpenChatById($result['id']);
        }

        return redirect()
            ->with($result);
    }
}
