<?php

declare(strict_types=1);

namespace App\Services\RankingBan;

use App\Models\Importer\SqlInsert;
use App\Models\Repositories\RankingPosition\RankingPositionHourPageRepositoryInterface;
use App\Models\Repositories\RankingPosition\RankingPositionPageRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use Shadow\DB;

class RankingBan
{
    function __construct(
        private RankingPositionPageRepositoryInterface $rankingPositionRepo,
        private RankingPositionHourPageRepositoryInterface $rankingPositionHourRepo,
        private StatisticsPageRepositoryInterface $statisticsRepo,
        private SqlInsert $sqlInsert,
    ) {
    }

    function updateRankingBanTable()
    {
        DB::$pdo = null;
        DB::connect();

        $ocs = DB::fetchAll(
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

        $latestTime = OpenChatServicesUtility::getModifiedCronTime('now');
        $result = [];
        $existsDelete = DB::fetchAll("SELECT id FROM ranking_ban", args: [\PDO::FETCH_COLUMN, 0]);
        foreach ($ocs as $oc) {
            $id = $oc['id'];
            $member = $oc['member'];

            $ranking = $this->rankingPositionHourRepo->getFinalRankingPosition($id, $oc['category']);
            if ($ranking && new \DateTime($ranking['time']) >= $latestTime) {
                continue;
            }

            if ($key = array_search($id, $existsDelete)) {
                unset($existsDelete[$key]);
                continue;
            }

            $rankingDay = $this->rankingPositionRepo->getFinalRankingPosition($id, $oc['category']);
            if (!$rankingDay) continue;

            if ($ranking) {
                $datetime = $ranking['time'];
            } else {
                $datetime = substr($rankingDay['time'], 0, 10);
                $member = $this->statisticsRepo->getMemberCount($id, $datetime) ?: $member;
            }

            $percentage = round($rankingDay['position'] / $rankingDay['total_count_ranking'] * 100);
            if ($percentage > 100) $percentage = 100;
            if ($percentage < 1) $percentage = 1;

            $result[] = compact('id', 'datetime', 'percentage', 'member');
        }

        foreach ($existsDelete as $id) {
            DB::connect()->exec("DELETE FROM ranking_ban WHERE id = {$id}");
        }

        $this->sqlInsert->import(DB::connect(), 'ranking_ban', $result);
    }
}
