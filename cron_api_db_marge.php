<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\Cron\SyncOpenChat;
use App\Services\CronJson\SyncOpenChatState;
use App\Services\OpenChat\OpenChatCrawlingFromApi;
use App\Services\OpenChat\OpenChatCrawlingFromPage;
use App\Services\Admin\AdminTool;
use App\Services\OpenChat\SubCategory\OpenChatSubCategorySynchronizer;
use App\Services\RankingPosition\RankingPositionHourUpdater;

set_time_limit(3600 * 4);

/**
 * @var SyncOpenChatState $syncOpenChatState
 */
$syncOpenChatState = app(SyncOpenChatState::class);
if ($state->isActive) {
    AdminTool::sendLineNofity('SyncOpenChat: state is active');
    exit;
}

/**
 * @var SyncOpenChat $syncOpenChat
 */
$syncOpenChat = app(SyncOpenChat::class, ['state' => $syncOpenChatState]);
try {
    $syncOpenChat->migrate();
    $syncOpenChat->update(app(OpenChatCrawlingFromApi::class));
    $syncOpenChat->update(app(OpenChatCrawlingFromPage::class));

    $syncOpenChat->finalizeMigrate();
    $syncOpenChat->finalizeUpdate();
    $syncOpenChat->finalizeRanking();
} catch (\Throwable $e) {
    $syncOpenChat->addMessage('SyncOpenChat: ' . $e->__toString());
    AdminTool::sendLineNofity($syncOpenChat->getMessage());
}

addCronLog($syncOpenChat->getMessage());
unset($syncOpenChatState);
unset($syncOpenChat);

/**
 * @var RankingPositionHourUpdater $rankingPosition
 */
$rankingPosition = app(RankingPositionHourUpdater::class);
try {
    $rankingPosition->crawlRisingAndUpdateRankingPositionHourDb();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity('rankingPosition: ' . $e->__toString());
}

unset($rankingPosition);

/**
 * @var OpenChatSubCategorySynchronizer $subCategory
 */
$subCategory = app(OpenChatSubCategorySynchronizer::class);
try {
    $subCategory->syncSubCategoriesAll();
} catch (\Throwable $e) {
    AdminTool::sendLineNofity('subCategory: ' . $e->__toString());
}

unset($subCategory);