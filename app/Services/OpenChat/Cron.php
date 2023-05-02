<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Models\Repositories\StatisticsRepositoryInterface;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Models\Repositories\LogRepositoryInterface;
use App\Services\OpenChat\UpdateOpenChat;

class Cron
{
    private StatisticsRepositoryInterface $statistics;
    private UpdateOpenChatRepositoryInterface $updateRepository;
    private LogRepositoryInterface $logRepository;
    private UpdateOpenChat $updater;

    function __construct(
        StatisticsRepositoryInterface $statistics,
        UpdateOpenChatRepositoryInterface $updateRepository,
        LogRepositoryInterface $logRepository,
        UpdateOpenChat $updater,
    ) {
        $this->statistics = $statistics;
        $this->updateRepository = $updateRepository;
        $this->logRepository = $logRepository;
        $this->updater = $updater;
    }

    /**
     * @param int $interval `updated_at`の間隔を秒数で指定する
     * @param int $limit    一度の処理で何件更新するか
     */
    function handle(int $interval, int $limit): ?array
    {
        $idArray = $this->updateRepository->getOpenChatIdByPeriod(time() - $interval, $limit);
        if (empty($idArray)) {
            return null;
        }

        foreach ($idArray as $key => $id) {
            $this->update($id);
            //if (($key + 1) % 3 === 0) sleep(1);
            sleep(3);
        }
        return $idArray;
    }

    private function update(int $open_chat_id)
    {
        try {
            $result = $this->updater->update($open_chat_id);
        } catch (\RuntimeException $e) {
            $this->logRepository->logUpdateOpenChatError(0, $open_chat_id, 'null', 'null', $e->getMessage());
            return;
        }

        if (!$result) {
            return;
        } elseif ($result['updatedData']['member'] === null) {
            $this->statistics->addStatisticsRecord($open_chat_id, $result['databaseData']['member']);
            return;
        }

        $this->statistics->addStatisticsRecord($open_chat_id, $result['updatedData']['member']);
    }
}
