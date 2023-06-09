<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Models\Repositories\StatisticsRepositoryInterface;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\OpenChat\UpdateOpenChat;
use App\Config\AppConfig;
use App\Exceptions\NologOpenChatException;

class Cron
{
    // 連続エラーの許容回数
    private const MAX_CONTINUOUS_ERRORS_COUNT = 3;
    // クローリングの間隔 (秒)
    private const CRAWLING_INTERVAL = 1;

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
     * @return array|null        array: 更新対象となったID, null: 更新対象のレコードがない場合
     * @throws \RuntimeException 連続エラー回数が上限を超えた場合
     */
    function handle(): ?array
    {
        // DBから更新対象のレコードを取得する
        $idArray = $this->updateRepository->getUpdateTargetOpenChatId(AppConfig::CRON_EXECUTE_COUNT);
        if (empty($idArray)) {
            return null;
        }

        // 連続エラーのカウンター
        $continuousErrorsCount = 0;

        foreach ($idArray as $id) {
            $result = $this->update($id);
            if ($result === false) {
                // エラーが発生した場合
                $continuousErrorsCount++;
            } else {
                $continuousErrorsCount = 0;
            }

            if ($continuousErrorsCount > self::MAX_CONTINUOUS_ERRORS_COUNT) {
                $this->logRepository->logUpdateOpenChatError(0, 'null', 'null', '連続エラー回数が上限を超えました。');
                throw new \RuntimeException('連続エラー回数が上限を超えました。');
            }

            // 次のクローリングまでの間隔を空ける
            sleep(self::CRAWLING_INTERVAL);
        }

        return $idArray;
    }

    /**
     * オープンチャットのレコードを更新する
     * 
     * @return null|bool true: 正常に処理が完了した場合, false: 404以外のエラーが発生した場合, null: 404の場合
     */
    private function update(int $open_chat_id): ?bool
    {
        try {
            // オープンチャットのページからデータを取得して、オープンチャットテーブルを更新する
            $result = $this->updater->update($open_chat_id);
        } catch (\RuntimeException $e) {
            $this->logRepository->logUpdateOpenChatError($open_chat_id, 'null', 'null', $e->getMessage());
            return false;
        } catch (NologOpenChatException $e) {
            // 収集拒否しているオープンチャットの場合
            $this->updateRepository->updateOpenChat($open_chat_id, false);
            $this->logRepository->logUpdateOpenChatError($open_chat_id, 'null', 'null', $e->getMessage());
            return null;
        }

        if (!$result) {
            // 404の場合
            return null;
        }

        // メンバー数統計テーブルを更新する
        if ($result['updatedData']['member'] !== null) {
            // メンバー数が更新されていた場合
            $this->statisticsRepository->insertUpdateDailyStatistics($open_chat_id, $result['updatedData']['member']);
            $this->updateRepository->updateNextUpdate($open_chat_id, strtotime('1 day'));
            return true;
        }

        // メンバー数に変化がない場合
        $this->statisticsRepository->insertUpdateDailyStatistics($open_chat_id, $result['databaseData']['member']);
        if ($this->updateRepository->getMemberChangeWithinLastWeek($open_chat_id)) {
            // 過去一週間でメンバー数に動きがある場合
            $this->updateRepository->updateNextUpdate($open_chat_id, strtotime('1 day'));
        } else {
            // 過去一週間でメンバー数に動きがない場合
            $this->updateRepository->updateNextUpdate($open_chat_id, strtotime('7 day'));
        }
        return true;
    }
}
