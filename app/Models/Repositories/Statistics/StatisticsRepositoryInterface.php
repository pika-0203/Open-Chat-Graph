<?php

declare(strict_types=1);

namespace App\Models\Repositories\Statistics;

use App\Services\OpenChat\Dto\OpenChatDto;

interface StatisticsRepositoryInterface
{
    public function addNewOpenChatStatisticsFromDto(OpenChatDto $dto): void;

    /**
     * @param string $date Y-m-d
     */
    public function insertDailyStatistics(int $open_chat_id, int $member, string $date): void;

    public function deleteDailyStatistics(int $open_chat_id): void;

    /**
     * @return int[] open_chat_id
     */
    public function getMemberChangeWithinLastWeekCacheArray(string $date): array;

    /**
     * @return int[] open_chat_id
     */
    public function getHourMemberChangeWithinLastWeekArray(string $date): array;

    /**
     * @param array{ open_chat_id: int, member: int, date: string }[] $data
     */
    public function insertMember(array $data): int;

    /**
     * @param string $date Y-m-d
     * @return int[]
     */
    public function getOpenChatIdArrayByDate(string $date): array;
}
