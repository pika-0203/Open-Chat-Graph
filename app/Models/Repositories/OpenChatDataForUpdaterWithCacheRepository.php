<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatRepositoryDto;

class OpenChatDataForUpdaterWithCacheRepository implements OpenChatDataForUpdaterWithCacheRepositoryInterface
{
    private static ?array $openChatIdAndNextUpdateCache;
    private static ?array $openChatEmidCache;
    private static ?array $openChatDataCache;
    private static ?array $memberChangeWithinLastWeekCache;

    public function __construct(
        private StatisticsRepositoryInterface $statisticsRepository
    ) {
    }

    public static function clearCache(): void
    {
        self::$openChatIdAndNextUpdateCache = null;
        self::$openChatEmidCache = null;
        self::$openChatDataCache = null;
        self::$memberChangeWithinLastWeekCache = null;
    }

    public static function addOpenChatIdByEmidCache(int $id, string $emid): void
    {
        $next_update = 0;
        $img_url = '';
        self::$openChatIdAndNextUpdateCache[] = compact('id', 'emid', 'next_update', 'img_url');
    }

    private function cacheOpenChatIdByEmid(): void
    {
        $query =
            "SELECT
                id,
                emid,
                next_update <= :curDate AS next_update,
                img_url
            FROM
                open_chat
            ORDER BY
                id ASC";

        self::$openChatIdAndNextUpdateCache = DB::fetchAll($query, ['curDate' => date('Y-m-d', time())]);
        self::$openChatEmidCache = array_column(self::$openChatIdAndNextUpdateCache, 'emid');
    }

    public function getOpenChatIdByEmid(string $emid): array|false
    {
        if (!isset(self::$openChatIdAndNextUpdateCache)) {
            $this->cacheOpenChatIdByEmid();
        }

        $search = array_search($emid, self::$openChatEmidCache);

        if ($search === false) {
            return false;
        }

        $result = self::$openChatIdAndNextUpdateCache[$search];
        if ($result['next_update']) {
            self::$openChatIdAndNextUpdateCache[$search]['next_update'] = 0;
        }

        return $result;
    }

    private function cacheOpenChatData(): void
    {
        $query =
            'SELECT
                id,
                emid,
                name,
                description,
                img_url,
                member,
                api_created_at,
                category,
                emblem
            FROM
                open_chat';

        self::$openChatDataCache = DB::fetchAll($query, null, [\PDO::FETCH_UNIQUE | \PDO::FETCH_ASSOC]);
    }

    public function getOpenChatDataById(int $id): OpenChatRepositoryDto|false
    {
        if (!isset(self::$openChatDataCache)) {
            $this->cacheOpenChatData();
        }

        $oc = self::$openChatDataCache[$id] ?? false;

        return $oc ? new OpenChatRepositoryDto($oc) : false;
    }

    public function getMemberChangeWithinLastWeek(int $open_chat_id): bool
    {
        if (!isset(self::$memberChangeWithinLastWeekCache)) {
            $this->cacheMemberChangeWithinLastWeek();
        }

        return in_array($open_chat_id, self::$memberChangeWithinLastWeekCache);
    }

    private function cacheMemberChangeWithinLastWeek(): void
    {
        self::$memberChangeWithinLastWeekCache = $this->statisticsRepository->getMemberChangeWithinLastWeekCacheArray();
    }
}
