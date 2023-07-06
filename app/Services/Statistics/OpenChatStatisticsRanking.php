<?php

declare(strict_types=1);

namespace App\Services\Statistics;

use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Services\Traits\TraitPaginationRecordsCalculator;

class OpenChatStatisticsRanking
{
    use TraitPaginationRecordsCalculator;

    private OpenChatListRepositoryInterface $openChatListRepository;

    function __construct(OpenChatListRepositoryInterface $openChatListRepository)
    {
        $this->openChatListRepository = $openChatListRepository;
    }

    /**
     * @return array|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int]`
     */
    function getMemberRanking(int $pageNumber, int $limit): array|false
    {
        return $this->get(
            $pageNumber,
            $limit,
            $this->openChatListRepository->getMemberRankingRecordCount(),
            $this->openChatListRepository->findMemberRanking(...)
        );
    }

    /**
     * @return array|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int]`
     */
    function getDailyRanking(int $pageNumber, int $limit): array|false
    {
        return $this->get(
            $pageNumber,
            $limit,
            $this->openChatListRepository->getDailyRankingRecordCount(),
            $this->openChatListRepository->findMemberStatsDailyRanking(...)
        );
    }

    /**
     * @return array|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int]`
     */
    function getPastWeekRanking(int $pageNumber, int $limit): array|false
    {
        return $this->get(
            $pageNumber,
            $limit,
            $this->openChatListRepository->getPastWeekRankingRecordCount(),
            $this->openChatListRepository->findMemberStatsPastWeekRanking(...)
        );
    }

    /**
     * @return array|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int]`
     */
    private function get(int $pageNumber, int $limit, int $totalRecords, \Closure $repository): array|false
    {
        // ページの最大数を取得する
        $maxPageNumber = $this->calcMaxPages($totalRecords, $limit);

        if ($pageNumber > $maxPageNumber) {
            // 現在のページ番号が最大ページ番号を超えている場合
            return false;
        }

        // ランキングを取得する
        $openChatList = $repository($this->calcOffset($pageNumber, $limit), $limit * $pageNumber);

        // 説明文を半角140文字以内にする
        $this->trimDescriptions($openChatList);

        return compact('pageNumber', 'maxPageNumber', 'openChatList', 'totalRecords');
    }

    static function trimDescriptions(array &$openChatList)
    {
        foreach ($openChatList as &$oc) {
            $oc['description'] = mb_strimwidth($oc['description'], 0, 170, '…');
        }
    }
}
