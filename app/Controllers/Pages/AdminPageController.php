<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Config\AppConfig;
use App\Models\Repositories\Statistics\StatisticsRepositoryInterface;
use App\Models\SQLite\Repositories\RankingPosition\SqliteRankingPositionHourRepository;
use App\Services\UpdateRankingService;
use App\Services\StaticData\StaticDataGenerator;
use App\Services\Admin\AdminAuthService;
use Shadow\DB;
use App\Services\Admin\AdminTool;
use App\Services\OpenChat\OpenChatApiDbMerger;
use App\Models\SQLite\SQLiteStatistics;
use App\Services\OpenChat\OpenChatDailyCrawling;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistence;
use App\Services\SitemapGenerator;
use App\Services\UpdateHourlyMemberRankingService;
use Shared\Exceptions\NotFoundException;

class AdminPageController
{
    function __construct(AdminAuthService $adminAuthService)
    {
        if (!$adminAuthService->auth()) {
            throw new NotFoundException;
        }
    }

    function test()
    {
        $path = AppConfig::ROOT_PATH . 'test_exec.php';

        exec("/usr/bin/php8.2 {$path} >/dev/null 2>&1 &");

        return view('admin/admin_message_page', ['title' => 'exec', 'message' => $path . ' を実行しました。']);
    }

    function positiondb(RankingPositionHourPersistence $rankingPositionHourPersistence)
    {
        $rankingPositionHourPersistence->persistStorageFileToDb();
        echo 'done';
    }

    function hourlygenerank(UpdateHourlyMemberRankingService $hourlyMemberRanking)
    {
        $hourlyMemberRanking->update();
        echo 'done';
    }

    private function genesitemap(SitemapGenerator $sitemapGenerator)
    {
        $sitemapGenerator->generate();
        echo 'done';
    }

    private function recoveryyesterdaystats()
    {
        $exeption = [];

        $ranking = DB::fetchAll('SELECT * FROM statistics_ranking_day');
        foreach ($ranking as $oc) {
            $yesterday = SQLiteStatistics::fetchColumn("SELECT member FROM statistics WHERE date = '2024-01-14' AND open_chat_id = " . $oc['open_chat_id']);
            if (!$yesterday) {
                $exeption[] = $oc;
                continue;
            };

            $member = $yesterday + $oc['diff_member'];
            SQLiteStatistics::execute("UPDATE statistics SET member = {$member} WHERE date = '2024-01-15' AND open_chat_id = " . $oc['open_chat_id']);
        }

        var_dump($exeption);
    }

    function cookie(AdminAuthService $adminAuthService, ?string $key)
    {
        if (!$adminAuthService->registerAdminCookie($key)) {
            return false;
        }

        return view('admin/admin_message_page', ['title' => 'cookie取得完了', 'message' => 'アクセス用のcookieを取得しました']);
    }

    function generank(UpdateRankingService $updateRankingService)
    {
        $updateRankingService->update(OpenChatServicesUtility::getCronModifiedStatsMemberDate());

        return view('admin/admin_message_page', ['title' => 'updateStaticData done', 'message' => 'updateStaticData done']);
    }

    function genetop(StaticDataGenerator $staticDataGenerator)
    {
        $staticDataGenerator->updateStaticData();

        return view('admin/admin_message_page', ['title' => 'updateStaticData done', 'message' => 'updateStaticData done']);
    }

    function killmerge()
    {
        OpenChatApiDbMerger::enableKillFlag();

        return view('admin/admin_message_page', ['title' => 'OpenChatApiDbMerger', 'message' => 'OpenChatApiDbMergerを強制終了しました']);
    }

    function killdaily()
    {
        OpenChatDailyCrawling::enableKillFlag();
        return view('admin/admin_message_page', ['title' => 'OpenChatApiDbMerger', 'message' => 'OpenChatDailyCrawlingを強制終了しました']);
    }

    function phpinfo()
    {
        phpinfo();
    }
}
