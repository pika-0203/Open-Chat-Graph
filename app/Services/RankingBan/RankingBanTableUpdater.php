<?php

declare(strict_types=1);

namespace App\Services\RankingBan;

use App\Models\Importer\SqlInsert;
use App\Models\Repositories\RankingPosition\RankingPositionHourPageRepositoryInterface;
use App\Models\Repositories\RankingPosition\RankingPositionPageRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface;
use App\Services\OpenChat\Updater\OpenChatUpdaterFromApi;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use Shadow\DB;

class RankingBanTableUpdater
{
    private \DateTime $time;

    function __construct(
        private RankingPositionPageRepositoryInterface $rankingPositionRepo,
        private RankingPositionHourPageRepositoryInterface $rankingPositionHourRepo,
        private StatisticsPageRepositoryInterface $statisticsRepo,
        private SqlInsert $sqlInsert,
        private OpenChatUpdaterFromApi $openChatUpdaterFromApi,
    ) {
        $this->time = OpenChatServicesUtility::getModifiedCronTime('now');
        $this->time = new \DateTime('2024-03-29 00:30:00');
    }

    private function buildTableData(
        array $openChatArray,
        \DateTime $latestTime,
        array $existsIdArray
    ): array {
        $result = [];

        foreach ($openChatArray as $ocArrayKey => $oc) {
            $id = $oc['id'];
            $member = $oc['member'];

            $ranking = $this->rankingPositionHourRepo->getFinalRankingPosition($id, $oc['category']);
            if ($ranking && new \DateTime($ranking['time']) >= $latestTime) continue;

            if (in_array($id, $existsIdArray)) {
                unset($existsIdArray[array_search($id, $existsIdArray)]);
                continue;
            }

            $rankingDay = $this->rankingPositionRepo->getFinalRankingPosition($id, $oc['category']);
            if (!$rankingDay) continue;

            if ($ranking) {
                $rankingHourTime = new \DateTime($ranking['time']);
                $rankingHourTime->modify('+1hour');
                $datetime = $rankingHourTime->format('Y-m-d H:i:s');
            } else {
                $datetime = substr($rankingDay['time'], 0, 10);
                $member = $this->statisticsRepo->getMemberCount($id, $datetime) ?: $member;
            }

            $percentage = round($rankingDay['position'] / $rankingDay['total_count_ranking'] * 100);
            if ($percentage > 100) $percentage = 100;
            if ($percentage < 1) $percentage = 1;

            $result[] = compact(
                'datetime',
                'percentage',
                'member'
            ) + [
                'open_chat_id' => $id,
                'flag2' => 0
            ];
        }

        return [$result, $existsIdArray];
    }

    private function updateTable(\DateTime $latestTime, array $deleteIdArray)
    {
        $endDateTime = $latestTime->format('Y-m-d H:i:s');

        foreach ($deleteIdArray as $id) {
            DB::connect()->exec(
                "UPDATE 
                    ranking_ban 
                SET 
                    flag = 1,
                    end_datetime = '{$endDateTime}'
                WHERE
                    open_chat_id = {$id}"
            );
        }
    }

    function crawlUpdateDeleteOpenChat(array $ocArray, \DateTime $latestTime)
    {
        $pastHour = new \DateTime($latestTime->format('Y-m-d H:i:s'));
        $pastHour->modify('-1hour');

        $latestOcIdArray = [];
        foreach ($ocArray as $key => $oc) {
            $datetime = new \DateTime($oc['datetime']);

            if ($datetime < $pastHour || $datetime >= $latestTime) continue;

            $latestOcIdArray[$key] = $oc['open_chat_id'];
        }

        foreach ($latestOcIdArray as $key => $id) {
            $this->openChatUpdaterFromApi->fetchUpdateOpenChat($id, false);

            $updatedAt = DB::fetchColumn(
                "SELECT
                    updated_at
                FROM
                    open_chat
                WHERE
                    id = {$id}"
            );

            if (!$updatedAt) continue;
            if (new \DateTime($updatedAt) < $latestTime) continue;

            $ocArray[$key]['flag2'] = 1;
        }

        return $ocArray;
    }

    function updateRankingBanTable()
    {
        DB::$pdo = null;
        DB::connect();

        $openChatArray = DB::fetchAll(
            "SELECT
                oc.id,
                oc.category,
                oc.member
            FROM
                open_chat AS oc
                LEFT JOIN statistics_ranking_hour AS h24 ON oc.id = h24.open_chat_id
            WHERE
                h24.id IS NULL
                AND oc.api_created_at IS NOT NULL
                AND oc.category IS NOT NULL
                AND oc.category != 0"
        );

        $existsIdArray = DB::fetchAll(
            "SELECT
                open_chat_id 
            FROM 
                ranking_ban
            WHERE 
                flag = 0",
            args: [\PDO::FETCH_COLUMN, 0]
        );

        [$insertOcArray, $deleteIdArray] = $this->buildTableData($openChatArray, $this->time, $existsIdArray);
        $this->updateTable($this->time, $deleteIdArray);

        $result = $this->crawlUpdateDeleteOpenChat($insertOcArray, $this->time);

        $this->sqlInsert->import(DB::connect(), 'ranking_ban', $result);
    }
}
