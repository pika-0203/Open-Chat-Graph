<?php

declare(strict_types=1);

namespace App\Models\Repositories\Statistics;

use App\Services\OpenChat\Dto\OpenChatDto;

interface StatisticsRepositoryInterface
{
    public function addNewOpenChatStatisticsFromDto(OpenChatDto $dto): void;

    /**
     * 統計のレコードを追加・更新する
     * 
     * @param ?int $member nullの場合は現在のメンバー数で追加する
     */
    public function insertDailyStatistics(int $open_chat_id, int $member, int|string $date): void;

    public function daleteDailyStatistics(int $open_chat_id): void;

    public function getMemberChangeWithinLastWeek(int $open_chat_id): bool;

    public function getMemberChangeWithinLastWeekCacheArray(): array;
}
