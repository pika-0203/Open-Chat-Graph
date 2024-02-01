<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\Repositories\OpenChatPageRepository;
use App\Services\RankingPosition\RankingPositionChartArrayService;
use Shadow\Kernel\Response;

class RankingPositionApiController
{
    function __construct(
        private OpenChatPageRepository $openChatPageRepository
    ) {
        header('Access-Control-Allow-Origin: *');
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
}
