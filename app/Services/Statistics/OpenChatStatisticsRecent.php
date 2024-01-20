<?php

declare(strict_types=1);

namespace App\Services\Statistics;

use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Services\Traits\TraitPaginationRecordsCalculator;
use App\Config\AppConfig;

class OpenChatStatisticsRecent
{
    use TraitPaginationRecordsCalculator;

    public function __construct(
        private OpenChatListRepositoryInterface $openChatListRepository
    ) {
    }

    /**
     * @return array|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int, 'labelArray' => array]`
     */
    public function getAllOrderByRegistrationDate(int $pageNumber): array|false
    {
        $labelArray = $this->openChatListRepository->findAllOrderByIdAscCreatedAtColumn();

        return $this->get(
            $pageNumber,
            count($labelArray),
            $this->openChatListRepository->findAllOrderByIdDesc(...),
            $labelArray
        );
    }

    /**
     * @return array|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int, 'labelArray' => array]`
     */
    public function getRecentChanges(int $pageNumber): array|false
    {
        $labelArray = $this->openChatListRepository->findRecentArchiveAscArchivedAtColumn();

        return $this->get(
            $pageNumber,
            count($labelArray),
            $this->openChatListRepository->findRecentArchive(...),
            $labelArray
        );
    }

    /**
     * @return array|false `['pageNumber' => int, 'maxPageNumber' => int, 'openChatList' => array, 'totalRecords' => int, 'labelArray' => array]`
     */
    private function get(int $pageNumber, int $totalRecords, \Closure $repository, array $labelArray = []): array|false
    {
        $limit = AppConfig::OPEN_CHAT_LIST_LIMIT;

        // ページの最大数を取得する
        $maxPageNumber = $this->calcMaxPages($totalRecords, $limit);

        if ($pageNumber > $maxPageNumber) {
            // 現在のページ番号が最大ページ番号を超えている場合
            return false;
        }

        $repoArgs = [$this->calcOffset($pageNumber, $limit), $limit * $pageNumber];

        // リストを取得する
        $openChatList = $repository(...$repoArgs);

        return compact(
            'pageNumber',
            'maxPageNumber',
            'openChatList',
            'totalRecords',
            'labelArray',
        );
    }
}
