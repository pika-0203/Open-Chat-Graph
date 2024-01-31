<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\Repositories\OpenChatPageRepository;
use App\Services\RankingPosition\RankingPositionChartArrayService;
use Shadow\Kernel\Response;

class RankingPositionApiController
{
    function __construct(
        private RankingPositionChartArrayService $rankingPositionChartArrayService,
        private OpenChatPageRepository $openChatPageRepository
    ) {
        header('Access-Control-Allow-Origin: *');
    }

    function rankingPosition(int $open_chat_id, string $sort): Response|false
    {
        $category = $this->openChatPageRepository->getRankingPositionCategoryById($open_chat_id);
        if ($category === false) {
            return response($this->rankingPositionChartArrayService->getStatsChartArrayWithoutPosition($open_chat_id));
        }

        switch ($sort) {
            case 'ranking':
                return response($this->rankingPositionChartArrayService->getRankingPositionChartArray($open_chat_id, $category));
            case 'ranking_all':
                return response($this->rankingPositionChartArrayService->getRankingPositionChartArray($open_chat_id, 0));
            case 'rising':
                return response($this->rankingPositionChartArrayService->getRisingPositionChartArray($open_chat_id, $category));
            case 'rising_all':
                return response($this->rankingPositionChartArrayService->getRisingPositionChartArray($open_chat_id, 0));
        }
    }
}
