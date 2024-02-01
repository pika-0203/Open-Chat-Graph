<?php

declare(strict_types=1);

namespace App\Controllers\Cron;

use App\Config\AppConfig;
use App\Services\CronJson\SyncOpenChatState;
use App\Services\OpenChat\OpenChatCrawlingFromApi;
use App\Services\UpdateRankingService;
use App\Services\OpenChat\OpenChatApiDbMerger;
use App\Services\OpenChat\DuplicateOpenChatMeger;
use App\Services\GceDifferenceUpdater;
use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepository;
use App\Models\Repositories\Log\LogRepositoryInterface;
use App\Models\Repositories\OpenChatRepository;
use App\Services\SitemapGenerator;
use Shadow\DB;

class SyncOpenChat
{
    private array $messages = [];

    function __construct(
        private SyncOpenChatState $state,
        private OpenChatApiDbMerger $merger,
        private GceDifferenceUpdater $gce,
        private DuplicateOpenChatMeger $dupMeger,
        private UpdateRankingService $updateRankingService,
        private LogRepositoryInterface $log,
        private SitemapGenerator $sitemapGenerator
    ) {
        $this->state->isActive = true;
        $this->state->update();
    }

    function __destruct()
    {
        $this->state->isActive = false;
        $this->state->update();
    }

    function addMessage(string $message): void
    {
        $this->messages[] = date('Y-m-d H:i:s') . ' ' . $message;
    }

    function getMessage(): string
    {
        return implode("\n", $this->messages);
    }

    function migrate(bool $updateFlag = true): void
    {
        OpenChatApiDbMerger::disableKillFlag();

        $max = $this->merger->countMaxExecuteNum(1);
        $this->addMessage("start migrate 1/{$max}");

        $category = array_keys(AppConfig::OPEN_CHAT_CATEGORY);
        $totalResult = 0;
        $totalInsert = 0;

        for ($i = 1; $i <= $max; $i++) {
            $result = $this->merger->fetchOpenChatApiRankingAll(1, $i, $updateFlag);

            $categoryName = $category[$i - 1];
            if (!$result) {
                throw new \RuntimeException("failed {$categoryName} {$i}: " . $this->log->getRecentLog());
            }

            $insertCount = OpenChatRepository::getInsertCount();
            OpenChatRepository::resetInsertCount();
            $totalInsert += $insertCount;

            $this->addMessage("{$categoryName} {$i} result: {$result}, insert: {$insertCount}");
            $totalResult += $result;
        }

        $this->addMessage("done migrate total: {$totalResult}, insert: {$totalInsert}");
        OpenChatDataForUpdaterWithCacheRepository::clearCache();
        DB::$pdo = null;
    }

    function update(OpenChatCrawlingFromApi $openChatCrawling): void
    {
        $className = getClassSimpleName($openChatCrawling);
        $count = $openChatCrawling->caluclatemaxExecuteNum(null);

        $this->addMessage("Start {$className}: " . $count[0]);
        $openChatCrawling->openChatCrawling(null);
        $this->addMessage("done {$className}\n");

        OpenChatDataForUpdaterWithCacheRepository::clearCache();
    }

    function finalizeMigrate(): void
    {
        $this->gce->finalizeSyncLatest();
        $this->addMessage("[GCE] done");
    }

    function finalizeUpdate(): void
    {
        $result = $this->dupMeger->mergeDuplicateOpenChat();
        $this->addMessage("mergeDuplicateOpenChat: " . count($result));

        $this->gce->finalizeOpenChatMerged();
        $this->addMessage("[GCE] done");

        $this->sitemapGenerator->generate();
    }

    function finalizeRanking(): void
    {
        [$resultRowCount, $resultPastWeekRowCount] = $this->updateRankingService->update();
        $this->addMessage("updateRankingService: [day: {$resultRowCount}, week: {$resultPastWeekRowCount}]");

        $this->gce->gceUpdateRanking();
        $this->addMessage("[GCE] done");
    }
}
