<?php

declare(strict_types=1);

namespace App\Services\RankingBan;

use App\Config\AppConfig;
use App\Models\Importer\SqlInsert;
use App\Models\Repositories\RankingPosition\RankingPositionHourPageRepositoryInterface;
use App\Models\Repositories\RankingPosition\RankingPositionPageRepositoryInterface;
use App\Models\Repositories\Statistics\StatisticsPageRepositoryInterface;
use App\Services\OpenChat\Updater\OpenChatUpdaterFromApi;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use App\Models\Repositories\DB;

/**
 * ランキングから除外されたOpenChatを検出し、ranking_banテーブルで管理するクラス
 * 
 * 主な役割:
 * 1. 24時間ランキング(statistics_ranking_hour)に存在しないOpenChatを検出
 * 2. ランキングから消えた理由（削除、非公開化など）を調査
 * 3. ranking_banテーブルにバン情報を記録・更新
 */
class RankingBanTableUpdater
{
    // 現在のcron実行時刻（分単位で丸められた時刻）
    private \DateTime $time;

    /**
     * @param RankingPositionPageRepositoryInterface $rankingPositionRepo 日次ランキング順位を取得
     * @param RankingPositionHourPageRepositoryInterface $rankingPositionHourRepo 時間別ランキング順位を取得
     * @param StatisticsPageRepositoryInterface $statisticsRepo 統計情報（メンバー数など）を取得
     * @param SqlInsert $sqlInsert 一括INSERT処理用
     * @param OpenChatUpdaterFromApi $openChatUpdaterFromApi APIからOpenChat情報を更新
     */
    function __construct(
        private RankingPositionPageRepositoryInterface $rankingPositionRepo,
        private RankingPositionHourPageRepositoryInterface $rankingPositionHourRepo,
        private StatisticsPageRepositoryInterface $statisticsRepo,
        private SqlInsert $sqlInsert,
        private OpenChatUpdaterFromApi $openChatUpdaterFromApi,
    ) {
        // cron実行時刻を取得（分単位で丸められた時刻）
        $this->time = OpenChatServicesUtility::getModifiedCronTime('now');
        // デバッグ用：特定日時でテストする場合はコメントアウトを外す
        //$this->time = new \DateTime('2024-01-31 16:30:00');
    }

    /**
     * ranking_banテーブルに挿入するデータを構築
     * 
     * @param array $openChatArray 24時間ランキングに存在しないOpenChatのリスト
     * @param \DateTime $latestTime 最新の処理時刻
     * @param array $existsListArray 既にranking_banテーブルに存在する未処理レコード
     * @return array [新規挿入データ配列, 削除対象の既存レコード配列]
     */
    private function buildTableData(
        array $openChatArray,
        \DateTime $latestTime,
        array $existsListArray
    ): array {
        $result = [];

        // 既存レコードのOpenChat IDを配列化（高速検索用）
        $existsIdArray = array_column($existsListArray, 'open_chat_id');

        foreach ($openChatArray as $ocArrayKey => $oc) {
            $id = $oc['id'];
            $member = $oc['member'];

            // 時間別ランキングの最終位置を取得
            $ranking = $this->rankingPositionHourRepo->getFinalRankingPosition($id, $oc['category']);

            // 最新時刻以降のランキングデータがある場合はスキップ（まだアクティブ）
            if ($ranking && new \DateTime($ranking['time']) >= $latestTime) continue;

            // 既にranking_banテーブルに存在する場合
            if (in_array($id, $existsIdArray)) {
                // 削除対象リストから除外（アクティブなバン記録として維持）
                unset($existsListArray[array_search($id, $existsIdArray)]);
                continue;
            }

            // 日次ランキングの最終位置を取得（バン時のランキング情報保存用）
            $rankingDay = $this->rankingPositionRepo->getFinalRankingPosition($id, $oc['category']);
            if (!$rankingDay) continue;

            // バン開始日時の決定
            if ($ranking) {
                // 時間別ランキングがある場合：最後のランキング時刻+1時間
                $rankingHourTime = new \DateTime($ranking['time']);
                $rankingHourTime->modify('+1hour');
                $datetime = $rankingHourTime->format('Y-m-d H:i:s');
            } else {
                // 時間別ランキングがない場合：日次ランキングの日付を使用
                $datetime = substr($rankingDay['time'], 0, 10);
                // その日のメンバー数を統計テーブルから取得
                $member = $this->statisticsRepo->getMemberCount($id, $datetime) ?: $member;
            }

            // ランキング順位のパーセンタイル計算（1-100%）
            $percentage = round($rankingDay['position'] / $rankingDay['total_count_ranking'] * 100);
            if ($percentage > 100) $percentage = 100;
            if ($percentage < 1) $percentage = 1;

            // ranking_banテーブル用のレコード作成
            $result[] = compact(
                'datetime',     // バン開始日時
                'percentage',   // 最終ランキング順位のパーセンタイル
                'member'        // 最終メンバー数
            ) + [
                'open_chat_id' => $id,
                'updated_at' => 0,        // 後でAPI更新時に1に変更
                'update_items' => null    // 後でAPI更新時に取得
            ];
        }

        return [$result, $existsListArray];
    }

    /**
     * ランキングに復活したOpenChatのバン記録を終了させる
     * 
     * @param \DateTime $latestTime 終了日時として設定する時刻
     * @param array $deleteListArray ランキングに復活したOpenChatのバン記録リスト
     */
    private function updateTable(\DateTime $latestTime, array $deleteListArray)
    {
        $endDateTime = $latestTime->format('Y-m-d H:i:s');

        foreach ($deleteListArray as $row) {
            $id = $row['open_chat_id'];
            $datetime = $row['datetime'];

            // 現在のOpenChat情報を取得（更新履歴確認用）
            $oc = DB::fetch(
                "SELECT
                    updated_at,
                    update_items
                FROM
                    open_chat
                WHERE
                    id = {$id}"
            );

            // バン期間中に更新があった場合は更新内容も記録
            if ($oc && $oc['update_items'] && new \DateTime($oc['updated_at']) >= new \DateTime($datetime)) {
                // flag=1：バン終了、end_datetime：復活確認時刻、update_items：バン期間中の変更内容
                DB::execute(
                    "UPDATE 
                        ranking_ban 
                    SET 
                        flag = 1,
                        end_datetime = '{$endDateTime}',
                        update_items = :update_items
                    WHERE
                        open_chat_id = {$id}
                        AND flag = 0",
                    ['update_items' => $oc['update_items']]
                );
            } else {
                // バン期間中に更新がない場合は終了日時のみ設定
                DB::connect()->exec(
                    "UPDATE 
                        ranking_ban 
                    SET 
                        flag = 1,
                        end_datetime = '{$endDateTime}'
                    WHERE
                        open_chat_id = {$id}
                        AND flag = 0"
                );
            }
        }
    }

    /**
     * 新規バン対象のOpenChatをAPIから最新情報に更新し、削除理由を取得
     * 
     * @param array $ocArray バン対象のOpenChat配列
     * @param \DateTime $latestTime 最新時刻（これ以降にバンされたものを対象）
     * @return array 更新情報を含むOpenChat配列
     */
    function crawlUpdateDeleteOpenChat(array $ocArray, \DateTime $latestTime)
    {
        // 最新時刻以降にバンされたOpenChatのみを抽出
        $latestOcIdArray = [];
        foreach ($ocArray as $key => $oc) {
            $datetime = new \DateTime($oc['datetime']);

            // 古いバン記録はスキップ
            if ($datetime < $latestTime) continue;

            $latestOcIdArray[$key] = $oc['open_chat_id'];
        }

        // 各OpenChatの最新情報をAPIから取得
        foreach ($latestOcIdArray as $key => $id) {
            // APIから最新情報を取得（削除・非公開化などの確認）
            $this->openChatUpdaterFromApi->fetchUpdateOpenChat($id, false);

            // 更新後の情報を取得
            $oc = DB::fetch(
                "SELECT
                    updated_at,
                    update_items
                FROM
                    open_chat
                WHERE
                    id = {$id}"
            );

            // OpenChatが存在しない（完全削除された）場合はスキップ
            if (!$oc) continue;

            // 更新情報がない、または古い更新の場合はスキップ
            if (!$oc['update_items'] || new \DateTime($oc['updated_at']) < $latestTime) continue;

            // バン理由（削除・非公開化など）を記録
            $ocArray[$key]['updated_at'] = 1;  // API更新済みフラグ
            $ocArray[$key]['update_items'] = $oc['update_items'];  // 変更内容（JSON形式）
        }

        return $ocArray;
    }

    /**
     * ランキングバンテーブルを更新するメイン処理
     * 
     * 処理フロー：
     * 1. 24時間ランキングに存在しないOpenChatを検出
     * 2. 既存のバン記録と照合
     * 3. 新規バン対象をAPIで調査（削除理由確認）
     * 4. ランキング復活したものは終了処理
     * 5. 新規バン情報をranking_banテーブルに記録
     */
    function updateRankingBanTable()
    {
        // 24時間ランキングに存在しないOpenChatを取得
        // LEFT JOINでstatistics_ranking_hourテーブルと結合し、
        // h24.id IS NULLで24時間ランキングに存在しないものを抽出
        $openChatArray = DB::fetchAll(
            "SELECT
                oc.id,
                oc.category,
                oc.member
            FROM
                open_chat AS oc
                LEFT JOIN statistics_ranking_hour AS h24 ON oc.id = h24.open_chat_id
            WHERE
                h24.id IS NULL                    -- 24時間ランキングに存在しない
                AND oc.api_created_at IS NOT NULL -- API経由で作成された（有効なOpenChat）
                AND oc.category IS NOT NULL       -- カテゴリが設定されている
                AND oc.category != 0              -- カテゴリ0（未分類）は除外
            "
        );

        // 現在アクティブなバン記録を取得（flag=0：バン中）
        $existsListArray = DB::fetchAll(
            "SELECT
                open_chat_id,
                datetime
            FROM 
                ranking_ban
            WHERE 
                flag = 0  -- flag=0：バン中、flag=1：バン終了
            "
        );

        // 開発環境の場合、処理数を制限（大量データ処理の負荷軽減）
        if (AppConfig::$isDevlopment ?? false) {
            $limit = AppConfig::$developmentEnvUpdateLimit['RankingBanTableUpdater'] ?? 1;
            $openChatArrayCount = count($openChatArray);
            $openChatArray = array_slice($openChatArray, 0, $limit);
            $existsListArray = array_slice($existsListArray, 0, $limit);
            addCronLog("Development environment. Update limit: {$limit} / {$openChatArrayCount}");
        }

        // バンテーブルのデータを構築
        // $insertOcArray: 新規挿入するバン記録
        // $deleteListArray: ランキングに復活したため終了処理するバン記録
        [$insertOcArray, $deleteListArray] = $this->buildTableData($openChatArray, $this->time, $existsListArray);

        // ランキング復活したOpenChatのバン記録を終了
        $this->updateTable($this->time, $deleteListArray);

        // 新規バン対象のOpenChatをAPIで調査（削除・非公開化の理由を取得）
        $result = $this->crawlUpdateDeleteOpenChat($insertOcArray, $this->time);

        // 新規バン記録をranking_banテーブルに一括挿入
        $this->sqlInsert->import(DB::connect(), 'ranking_ban', $result);
    }
}
