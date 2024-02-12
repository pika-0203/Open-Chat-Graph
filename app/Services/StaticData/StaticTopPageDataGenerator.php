<?php

declare(strict_types=1);

namespace App\Services\StaticData;

use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Config\AppConfig;

class StaticTopPageDataGenerator
{
    function __construct(
        private OpenChatListRepositoryInterface $openChatListRepository,
    ) {
    }

    /**
     * @return array{ openChatList: array, pastWeekOpenChatList: array, updatedAt: int }
     */
    function getTopPageDataFromDB(): array
    {
        // トップページのキャッシュファイルを生成する
        $rankingList = [];
        $rankingList['openChatList'] = $this->openChatListRepository->findMemberStatsDailyRanking(0, 10);
        $rankingList['pastWeekOpenChatList'] = $this->openChatListRepository->findMemberStatsPastWeekRanking(0, 10);

        $data = file_get_contents(AppConfig::TOP_RANKING_INFO_FILE_PATH);
        [
            'rankingUpdatedAt' => $rankingList['updatedAt'],
            'rankingRowCount' => $rankingList['dailyRankingRowCount'],
            'recordCount' => $rankingList['recordCount'],
        ] = unserialize($data);

        $rankingList['recordCount'] = $this->openChatListRepository->getRecordCount();

        // 説明文の文字数を詰める
        trimOpenChatListDescriptions($rankingList['openChatList']);
        trimOpenChatListDescriptions($rankingList['pastWeekOpenChatList']);

        return $rankingList;
    }

    function updateStaticTopPageData()
    {
        $rankingList = $this->getTopPageDataFromDB();

        saveSerializedArrayToFile('static_data_top/ranking_list.dat', $rankingList);
    }
}
