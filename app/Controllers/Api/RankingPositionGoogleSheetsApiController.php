<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Config\AppConfig;
use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\CronJson\RankingPositionHourUpdaterState;
use App\Services\OpenChat\Crawler\OpenChatCrawler;
use App\Services\RankingPosition\RankingPositionHourApiService;

class RankingPositionGoogleSheetsApiController
{
    /**
     * {
     *  name: string;
     *  next_update: string | undifined;                  // ISO 8601
     *  category: string | undifined;                     // ランキングデータ無しの場合 undifined
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
    function rankingPosition(
        OpenChatPageRepositoryInterface $ocRepo,
        RankingPositionHourApiService $service,
        RankingPositionHourUpdaterState $state,
        OpenChatCrawler $crawler,
        LogRepositoryInterface $logRepo,
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

        $categoryIndex = $categoryIndex ? $categoryIndex : 0;
        $categoryName = $categoryIndex ? AppConfig::OPEN_CHAT_CATEGORY_KEYS[$categoryIndex] : 'ランキング未掲載';

        $dto = $service->getLatestRanking((string)$emid, $categoryIndex);
        if (!$dto || $state->isActive) {
            // ランキング未更新・更新中の場合
            $next_update = $service->getNextUpdate()->modify('-2 minute')->format(\DateTime::ATOM);
            return response(compact('name', 'categoryName', 'next_update'));
        }

        $next_update = $service->getNextUpdate()->format(\DateTime::ATOM);
        $updated_at = $service->getCurrentTime()->format(\DateTime::ATOM);

        if (!$dto->member) {
            // ランキング圏外で最新のメンバー数を取得する場合
            try {
                $ocDto = $crawler->fetchOpenChatDto($oc['url']);
            } catch (\Throwable $e) {
                $logRepo->logUpdateOpenChatError($open_chat_id, $e->__toString());
                return response(compact('name', 'categoryName', 'next_update'));
            }

            if (!$ocDto) {
                return false;
            }

            $updated_at = (new \DateTime())->format(\DateTime::ATOM);
            $dto->member = $ocDto->memberCount;
        }

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
}
