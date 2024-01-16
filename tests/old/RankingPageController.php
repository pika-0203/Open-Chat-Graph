<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Statistics\OpenChatStatisticsRanking;
use App\Views\RankingView;

//class RankingPageController
{
    private OpenChatStatisticsRanking $rankingRepositoryWrapper;
    private RankingView $rankingView;

    public function __construct(
        OpenChatStatisticsRanking $rankingRepositoryWrapper,
        RankingView $rankingView,
    ) {
        $this->rankingRepositoryWrapper = $rankingRepositoryWrapper;
        $this->rankingView = $rankingView;
    }

    public function index(?int $pageNumber)
    {
        $dto = $this->rankingRepositoryWrapper->getMemberRanking($pageNumber ?? 1);
        if (!$dto) {
            return false;
        }

        trimOpenChatListDescriptions($dto->openChatList);

        $dto->disabledBtnName = 'member';
        $dto->pagePath = 'ranking';
        $dto->noindex = ($pageNumber ?? 1) === 1 ? false : true;

        $dto = $this->rankingView->finalizeRankingViewDto($dto);

        return view('ranking_content', compact('dto'));
    }

    public function daily(?int $pageNumber)
    {
        $dto = $this->rankingRepositoryWrapper->getDailyRanking($pageNumber ?? 1);
        if (!$dto) {
            return false;
        }

        trimOpenChatListDescriptions($dto->openChatList);

        $dto->disabledBtnName = 'daily';
        $dto->pagePath = 'ranking/daily';
        $dto->noindex = true;

        $dto = $this->rankingView->finalizeRankingViewDto($dto);

        return view('ranking_content', compact('dto'));
    }

    public function weekly(?int $pageNumber)
    {
        $dto = $this->rankingRepositoryWrapper->getPastWeekRanking($pageNumber ?? 1);
        if (!$dto) {
            return false;
        }

        trimOpenChatListDescriptions($dto->openChatList);

        $dto->disabledBtnName = 'weekly';
        $dto->pagePath = 'ranking/weekly';
        $dto->noindex = true;

        $dto = $this->rankingView->finalizeRankingViewDto($dto);

        return view('ranking_content', compact('dto'));
    }
}
