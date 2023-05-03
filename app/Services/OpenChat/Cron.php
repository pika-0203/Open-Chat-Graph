<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Models\Repositories\StatisticsRepositoryInterface;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\OpenChat\UpdateOpenChat;

class Cron
{
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
     */
    function handle(int $interval, int $limit): ?array
    {
        // DBから更新対象のレコードを取得する
        $idArray = $this->updateRepository->getOpenChatIdByPeriod(time() - $interval, $limit);
        if (empty($idArray)) {
            return null;
        }

        foreach ($idArray as $key => $id) {
            $this->update($id);
            // 次のクローリングまでの間隔を空ける (3秒)
            sleep(3);
        }
        return $idArray;
    }

    private function update(int $open_chat_id)
    {
        try {
            // オープンチャットのページからデータを取得する
            $result = $this->updater->update($open_chat_id);
        } catch (\RuntimeException $e) {
            $this->logRepository->logUpdateOpenChatError(0, $open_chat_id, 'null', 'null', $e->getMessage());
            return;
        }

        if (!$result) {
            // 404の場合
            return;
        }
        
        // メンバー数の統計テーブルにレコードを追加する
        if ($result['updatedData']['member'] === null) {
            // メンバー数に変化がない場合
            $this->statisticsRepository->addStatisticsRecord($open_chat_id, $result['databaseData']['member']);
        } else {
            // メンバー数が更新されていた場合
            $this->statisticsRepository->addStatisticsRecord($open_chat_id, $result['updatedData']['member']);
        }
    }
}
