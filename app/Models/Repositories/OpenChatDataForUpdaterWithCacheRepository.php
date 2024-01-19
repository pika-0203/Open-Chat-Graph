<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use Shadow\DB;
use App\Config\AppConfig;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatRepositoryDto;
use App\Services\OpenChat\Dto\OpenChatDto;

class OpenChatDataForUpdaterWithCacheRepository implements OpenChatDataForUpdaterWithCacheRepositoryInterface
{
    private static ?array $openChatIdAndNextUpdateCache;
    private static ?array $openChatEmidCache;
    private static ?array $openChatDataCache;
    private static ?array $memberChangeWithinLastWeekCache;
    private StatisticsRepositoryInterface $statisticsRepository;

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
        self::$openChatIdAndNextUpdateCache[] = compact('id', 'emid', 'next_update');
    }

    public function __construct(StatisticsRepositoryInterface $statisticsRepository)
    {
        $this->statisticsRepository = $statisticsRepository;
    }

    private function cacheOpenChatIdByEmid(): void
    {
        $query =
            "SELECT
                id,
                emid,
                next_update <= :curDate AS next_update
            FROM
                open_chat
            WHERE
                emid IS NOT NULL
                AND emid != ''
                AND is_alive = 1
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
                emblem,
                url
            FROM
                open_chat AS oc
            WHERE
                is_alive = 1';

        self::$openChatDataCache = DB::fetchAll($query);
    }

    public function findDuplicateOpenChat(OpenChatDto $dto): int|false
    {
        if (!isset(self::$openChatDataCache)) {
            $this->cacheOpenChatData();
        }

        $imgUrl = $dto->profileImageObsHash;
        $search = false;

        if (!in_array($imgUrl, AppConfig::DEFAULT_OPENCHAT_IMG_URL)) {
            foreach (self::$openChatDataCache as $key => $oc) {
                if ($oc['img_url'] === $imgUrl) {
                    $search = $key;
                    break;
                }
            }
        } else {
            foreach (self::$openChatDataCache as $key => $oc) {
                if (
                    $oc['img_url'] === $imgUrl
                    && $oc['description'] === $dto->desc
                    && $oc['name'] === $dto->name
                ) {
                    $search = $key;
                    break;
                }
            }
        }

        if ($search === false) {
            return false;
        }

        return self::$openChatDataCache[$search]['id'];
    }

    public function getOpenChatDataById(int $id): OpenChatRepositoryDto|false
    {
        if (!isset(self::$openChatDataCache)) {
            $this->cacheOpenChatData();
        }

        $search = false;
        foreach (self::$openChatDataCache as $key => $oc) {
            if ($oc['id'] === $id) {
                $search = $key;
                break;
            }
        }

        if ($search === false) {
            return false;
        }

        return new OpenChatRepositoryDto(self::$openChatDataCache[$search]);
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
