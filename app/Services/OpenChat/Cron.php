<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Models\Repositories\StatisticsRepositoryInterface;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\OpenChat\UpdateOpenChat;

class Cron
{
    // 断続エラーの許容回数
    private const MAX_CONSECUTIVE_ERRORS_COUNT = 3;
    // クローリングの間隔 (秒)
    private const CRAWLING_INTERVAL = 3;

    private StatisticsRepositoryInterface $statisticsRepository;
    private UpdateOpenChatRepositoryInterface $updateRepository;
    private LogRepositoryInterface $logRepository;
    private UpdateOpenChat $updater;

    function __construct(
        StatisticsRepositoryInterface $statisticsRepository,
        UpdateOpenChatRepositoryInterface $updateRepository,
        LogRepositoryInterface $logRepository,
        UpdateOpenChat $updater,
    ) {
        $this->statisticsRepository = $statisticsRepository;
        $this->updateRepository = $updateRepository;
        $this->logRepository = $logRepository;
        $this->updater = $updater;
    }

    /**
     * @param int $interval `updated_at`の更新間隔を秒数で指定する  8時間で設定済み
     * @param int $limit    一度の処理で何件更新するか 　           200件で設定済み
     * 
     * @return array|null        array: 更新対象となったID, null: 更新対象のレコードがない場合
     * @throws \RuntimeException 断続的なエラーが発生した場合
     */
    function handle(int $interval, int $limit): ?array
    {
        // DBから更新対象のレコードを取得する
        $idArray = $this->updateRepository->getOpenChatIdByPeriod(time() - $interval, $limit);
        if (empty($idArray)) {
            return null;
        }

        // 断続エラーのカウンター
        $consecutiveErrorsCount = 0;

        foreach ($idArray as $id) {
            $result = $this->update($id);
            if ($result === false) {
                // エラーが発生した場合
                $consecutiveErrorsCount++;
            } else {
                $consecutiveErrorsCount = 0;
            }

            if ($consecutiveErrorsCount >= self::MAX_CONSECUTIVE_ERRORS_COUNT) {
                throw new \RuntimeException('断続的なエラーが発生しました。');
            }

            // 次のクローリングまでの間隔を空ける
            sleep(self::CRAWLING_INTERVAL);
        }

        return $idArray;
    }

    /**
     * オープンチャットのレコードを更新して、メンバー数の統計レコードを追加する
     * 
     * @return null|bool true: 正常に処理が完了した場合, false: 404以外のエラーが発生した場合, null: 404の場合
     */
    private function update(int $open_chat_id): ?bool
    {
        try {
            // オープンチャットのページからデータを取得する
            $result = $this->updater->update($open_chat_id);
        } catch (\RuntimeException $e) {
            $this->logRepository->logUpdateOpenChatError(0, $open_chat_id, 'null', 'null', $e->getMessage());
            return false;
        }

        if (!$result) {
            // 404の場合
            return null;
        }

        // メンバー数の統計テーブルにレコードを追加する
        if ($result['updatedData']['member'] === null) {
            // メンバー数に変化がない場合
            $this->statisticsRepository->addStatisticsRecord($open_chat_id, $result['databaseData']['member']);
        } else {
            // メンバー数が更新されていた場合
            $this->statisticsRepository->addStatisticsRecord($open_chat_id, $result['updatedData']['member']);
        }

        return true;
    }
}
