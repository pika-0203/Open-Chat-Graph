<?php

declare(strict_types=1);

namespace App\Services\Statistics;

use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Config\AppConfig;
use App\Services\Traits\TraitPaginationRecordsCalculator;

//class OpenChatStatisticsSearch
{
    use TraitPaginationRecordsCalculator;

    private OpenChatListRepositoryInterface $openChatListRepository;

    public function __construct(OpenChatListRepositoryInterface $openChatListRepository)
    {
        $this->openChatListRepository = $openChatListRepository;
    }

    /**
     * @return false|null|array false: ページ番号が最大数を超えている場合,  
     *                          array: `[]` 検索結果が0件の場合,  
     *                          array: `['pageNumber' => int, 'maxPageNumber' => int, 'count' => int, 'openChatList' => array]`
     */
    public function get(string $keyword, int $pageNumber): false|array
    {
        // 検索結果を取得する
        ['count' => $count, 'result' => $result] = $this->openChatListRepository->findByKeyword(
            $keyword,
            $this->calcOffset($pageNumber, AppConfig::OPEN_CHAT_LIST_LIMIT),
            AppConfig::OPEN_CHAT_LIST_LIMIT
        );

        if (empty($result) && $count > 0) {
            // ページ番号が最大数を超えている場合
            return false;
        } elseif (empty($result)) {
            // 検索結果が0件の場合
            return [];
        }

        // ページの最大数を取得する
        $pageNumber = $pageNumber;
        $maxPageNumber = $this->calcMaxPages(
            $count,
            AppConfig::OPEN_CHAT_LIST_LIMIT
        );

        trimOpenChatListDescriptions($result);

        return compact('pageNumber', 'maxPageNumber', 'count') + ['openChatList' => $result];
    }
}
