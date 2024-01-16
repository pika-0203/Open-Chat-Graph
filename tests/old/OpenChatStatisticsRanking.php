<?php

declare(strict_types=1);

namespace App\Services\Statistics;

use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Services\Traits\TraitPaginationRecordsCalculator;
use App\Views\RankingViewDto;
use App\Config\AppConfig;

//class OpenChatStatisticsRanking
{
    use TraitPaginationRecordsCalculator;

    private OpenChatListRepositoryInterface $openChatListRepository;

    public function __construct(OpenChatListRepositoryInterface $openChatListRepository,)
    {
        $this->openChatListRepository = $openChatListRepository;
    }

    /**
     * @return array|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int]`
     */
    public function getAllOrderByRegistrationDate(int $pageNumber): array|false
    {
        return $this->get(
            $pageNumber,
            $this->openChatListRepository->getRecordCount(),
            $this->openChatListRepository->findAllOrderByIdDesc(...)
        );
    }

    /**
     * @return array|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int]`
     */
    public function getRecentChanges(int $pageNumber): array|false
    {
        return $this->get(
            $pageNumber,
            $this->openChatListRepository->getRecentArchiveRecordCount(),
            $this->openChatListRepository->findRecentArchive(...)
        );
    }

    /**
     * @return RankingViewDto|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int]`
     */
    public function getMemberRanking(int $pageNumber): RankingViewDto|false
    {
        return (new RankingViewDto)->setProps($this->get(
            $pageNumber,
            $this->openChatListRepository->getRecordCount(),
            $this->openChatListRepository->findMemberRanking(...)
        ));
    }

    /**
     * @return RankingViewDto|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int]`
     */
    public function getDailyRanking(int $pageNumber): RankingViewDto|false
    {
        return (new RankingViewDto)->setProps($this->get(
            $pageNumber,
            $this->openChatListRepository->getDailyRankingRecordCount(),
            $this->openChatListRepository->findMemberStatsDailyRanking(...)
        ));
    }

    /**
     * @return RankingViewDto|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int]`
     */
    public function getPastWeekRanking(int $pageNumber): RankingViewDto|false
    {
        return (new RankingViewDto)->setProps($this->get(
            $pageNumber,
            $this->openChatListRepository->getPastWeekRankingRecordCount(),
            $this->openChatListRepository->findMemberStatsPastWeekRanking(...)
        ));
    }

    /**
     * @return array|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int]`
     */
    private function get(int $pageNumber, int $totalRecords, \Closure $repository): array|false
    {
        $limit = AppConfig::OPEN_CHAT_LIST_LIMIT;

        // ページの最大数を取得する
        $maxPageNumber = $this->calcMaxPages($totalRecords, $limit);

        if ($pageNumber > $maxPageNumber) {
            // 現在のページ番号が最大ページ番号を超えている場合
            return false;
        }

        // ランキングを取得する
        $openChatList = $repository($this->calcOffset($pageNumber, $limit), $limit * $pageNumber);

        return compact(
            'pageNumber',
            'maxPageNumber',
            'openChatList',
            'totalRecords'
        );
    }
}
