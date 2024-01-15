<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\UpdateRankingService;
use App\Services\StaticData\StaticTopPageDataGenerator;
use App\Services\Admin\AdminAuthService;
use Shadow\DB;
use App\Services\Admin\AdminTool;
use App\Config\OpenChatCrawlerConfig;
use App\Services\OpenChat\DuplicateOpenChatMeger;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\OpenChatCrawlingFromPage;
use App\Services\OpenChat\OpenChatApiDbMerger;
use App\Services\OpenChat\Store\OpenChatImageStore;
use App\Services\OpenChat\Crawler\OpenChatCrawler;
use App\Config\AppConfig;
use App\Exceptions\ApplicationException;
use App\Config\ConfigJson;
use App\Models\GCE\GceDbTableSynchronizer;
use App\Models\GCE\DBGce;
use App\Models\Importer\SqlInsert;
use App\Models\GCE\GceRankingUpdater;
use App\Services\GceDifferenceUpdater;
use App\Controllers\Cron\SyncOpenChat;
use App\Services\SitemapGenerator;
use App\Services\OpenChat\Updater\Process\OpenChatCrawlingProcess;
use App\Services\OpenChat\Updater\OpenChatUpdaterFromApi;

set_time_limit(3600 * 4);

$message = "start: " . date('Y-m-d H:i:s') . "\n\n";

//$gcedeiff->finalizeDifferenceUpdate();
try {
    $sql = app(GceDbTableSynchronizer::class);
    $gce = app(GceRankingUpdater::class);

    DBGce::execute("TRUNCATE TABLE open_chat");
    $message .= 'syncOpenChatAll: ' . $sql->syncOpenChatAll() . "\nend: " . date('Y-m-d H:i:s') . "\n\n";
    AdminTool::sendLineNofity($message);

    DBGce::execute("TRUNCATE TABLE open_chat_archive");
    $message .= 'syncOpenChatArchive: ' . $sql->syncOpenChatArchive(true) . "\nend: " . date('Y-m-d H:i:s') . "\n\n";
    DBGce::execute("TRUNCATE TABLE open_chat_merged");
    $message .= 'syncOpenChatMerged: ' . $sql->syncOpenChatMerged() . "\nend: " . date('Y-m-d H:i:s') . "\n\n";
    DBGce::execute("TRUNCATE TABLE user_registration_open_chat");
    $message .= 'syncUserRegistrationOpenChat: ' . $sql->syncUserRegistrationOpenChat() . "\nend: " . date('Y-m-d H:i:s') . "\n\n";
    $gce->updateRanking();
} catch (\Throwable $e) {
    $message = $e->getMessage();
}

AdminTool::sendLineNofity($message);
