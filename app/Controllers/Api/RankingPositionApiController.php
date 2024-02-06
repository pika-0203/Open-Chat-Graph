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
        string $sort
    ): Response|false {
        $category = $this->openChatPageRepository->getRankingPositionCategoryById($open_chat_id);
        if ($category === false) {
            return response($rankingPositionChartArrayService->getStatsChartArrayWithoutPosition($open_chat_id));
        }

        switch ($sort) {
            case 'ranking':
                return response($rankingPositionChartArrayService->getRankingPositionChartArray($open_chat_id, $category));
            case 'ranking_all':
                return response($rankingPositionChartArrayService->getRankingPositionChartArray($open_chat_id, 0));
            case 'rising':
                return response($rankingPositionChartArrayService->getRisingPositionChartArray($open_chat_id, $category));
            case 'rising_all':
                return response($rankingPositionChartArrayService->getRisingPositionChartArray($open_chat_id, 0));
        }
    }

    function rankingPositionHour(
        RankingPositionHourChartArrayService $rankingPositionHourChartArrayService,
        int $open_chat_id,
        string $sort
    ): Response|false {
        $category = $this->openChatPageRepository->getRankingPositionCategoryById($open_chat_id);
        if ($category === false) {
            return false;
        }

        switch ($sort) {
            case 'ranking':
                return response($rankingPositionHourChartArrayService->getRankingPositionHourChartArray($open_chat_id, $category));
            case 'ranking_all':
                return response($rankingPositionHourChartArrayService->getRankingPositionHourChartArray($open_chat_id, 0));
            case 'rising':
                return response($rankingPositionHourChartArrayService->getRisingPositionHourChartArray($open_chat_id, $category));
            case 'rising_all':
                return response($rankingPositionHourChartArrayService->getRisingPositionHourChartArray($open_chat_id, 0));
        }
    }
}
