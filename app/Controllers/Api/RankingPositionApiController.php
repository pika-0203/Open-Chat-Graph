<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\RankingPosition\RankingPositionChartArrayService;
use App\Services\RankingPosition\RankingPositionHourChartArrayService;
use Shadow\Kernel\Response;

class RankingPositionApiController
{
    function __construct(
        private OpenChatPageRepositoryInterface $openChatPageRepository
    ) {
        localCORS();
    }

    function rankingPosition(
        RankingPositionChartArrayService $rankingPositionChartArrayService,
        int $open_chat_id,
        int $category,
        string $sort,
        string $start_date,
        string $end_date
    ): Response|false {
        $startDate = new \DateTime($start_date);
        $endDate = new \DateTime($end_date);
        
        return response(
            $sort === 'ranking'
                ? $rankingPositionChartArrayService->getRankingPositionChartArray($open_chat_id, $category, $startDate, $endDate)
                : $rankingPositionChartArrayService->getRisingPositionChartArray($open_chat_id, $category, $startDate, $endDate)
        );
    }

    function rankingPositionHour(
        RankingPositionHourChartArrayService $rankingPositionHourChartArrayService,
        int $open_chat_id,
        int $category,
        string $sort
    ): Response|false {
        return response(
            $sort === 'ranking'
                ? $rankingPositionHourChartArrayService->getRankingPositionHourChartArray($open_chat_id, $category)
                : $rankingPositionHourChartArrayService->getRisingPositionHourChartArray($open_chat_id, $category)
        );
    }
}
