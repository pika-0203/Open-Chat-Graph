<?php

declare(strict_types=1);

namespace App\Services\RankingBan;

use App\Services\Traits\TraitPaginationRecordsCalculator;
use App\Config\AppConfig;
use App\Models\RankingBanRepositories\RankingBanPageRepository;

class RakingBanPageService
{
    use TraitPaginationRecordsCalculator;

    public function __construct(
        private RankingBanPageRepository $rankingBanPageRepository
    ) {
    }

    /**
     * @param bool $publish false:掲載中のみ, true:未掲載のみ
     * @param bool $change false:内容変更ありのみ, true:変更なしのみ
     * @return array{ pageNumber:int,maxPageNumber:int,openChatList:array,totalRecords:int,labelArray:array }
     */
    public function getAllOrderByDateTime(bool $change, bool $publish, int $percent, int $pageNumber): array|false
    {
        $labelArray = $this->rankingBanPageRepository->findAllDatetimeColumn($change, $publish, $percent);

        $limit = AppConfig::OPEN_CHAT_LIST_LIMIT;

        // ページの最大数を取得する
        $totalRecords = count($labelArray);
        $maxPageNumber = $this->calcMaxPages($totalRecords, $limit);
        if ($pageNumber > $maxPageNumber) {
            // 現在のページ番号が最大ページ番号を超えている場合
            return false;
        }

        // リストを取得する
        $list = $this->rankingBanPageRepository->findAllOrderByIdDesc(
            $change,
            $publish,
            $percent,
            $this->calcOffset($pageNumber, $limit),
            $limit
        );

        $openChatList = array_map(function ($oc) {
            if (!$oc['update_items']) return $oc;
            $oc['update_items'] = array_keys(
                array_filter(json_decode($oc['update_items'], true))
            );
            return $oc;
        }, $list);

        return compact(
            'pageNumber',
            'maxPageNumber',
            'openChatList',
            'totalRecords',
            'labelArray',
        );
    }
}
