<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Models\Repositories\OpenChatPageRepositoryInterface;
use App\Services\OpenChat\Enum\RankingType;
use App\Services\RankingPosition\RankingPositionChartArrayService;
use App\Services\RankingPosition\RankingPositionHourChartArrayService;

class RankingPositionApiController
{
    function __construct(
        private OpenChatPageRepositoryInterface $openChatPageRepository
    ) {
        localCORS();
    }

    function rankingPosition(
        RankingPositionChartArrayService $chart,
        int $open_chat_id,
        int $category,
        string $sort,
        string $start_date,
        string $end_date
    ) {
        return response($chart->getRankingPositionChartArray(
            RankingType::from($sort),
            $open_chat_id,
            $category,
            new \DateTime($start_date),
            new \DateTime($end_date)
        ));
    }

    function rankingPositionHour(
        RankingPositionHourChartArrayService $chart,
        int $open_chat_id,
        int $category,
        string $sort
    ) {
        return response($chart->getPositionHourChartArray(
            RankingType::from($sort),
            $open_chat_id,
            $category
        ));
    }
}
