<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\UpdateRankingService;
use App\Services\StaticData\StaticTopPageDataGenerator;
use App\Services\Admin\AdminAuthService;
use Shadow\DB;
use App\Services\Admin\AdminTool;
use App\Services\OpenChat\OpenChatApiDbMerger;
use App\Models\SQLite\SQLiteStatistics;
use App\Services\Cron\CronJson\SyncOpenChatState;
use App\Services\RankingPosition\Persistence\RankingPositionDailyPersistence;
use App\Services\RankingPosition\Persistence\RankingPositionHourPersistence;
use App\Services\RankingPosition\RankingPositionHourUpdater;
use App\Services\SitemapGenerator;
use Shared\Exceptions\NotFoundException;

class AdminPageController
{
    function __construct(AdminAuthService $adminAuthService)
    {
        if (!$adminAuthService->auth()) {
            throw new NotFoundException;
        }
    }

    private function position()
    {
        /**
         * @var RankingPositionHourUpdater $rankingPosition
         */
        $rankingPosition = app(RankingPositionHourUpdater::class);
        try {
            $rankingPosition->crawlRisingAndUpdateRankingPositionHourDb();
        } catch (\Throwable $e) {
            AdminTool::sendLineNofity('rankingPosition: ' . $e->__toString());
            exit;
        }

        unset($rankingPosition);
    }

    private function positiondb(RankingPositionDailyPersistence $persistence)
    {
        $persistence->persistHourToDaily();
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

    private function generank(UpdateRankingService $updateRankingService)
    {
        $updateRankingService->update();

        return view('admin/admin_message_page', ['title' => 'updateStaticData done', 'message' => 'updateStaticData done']);
    }

    private function genetop(StaticTopPageDataGenerator $staticTopPageDataGenerator)
    {
        $staticTopPageDataGenerator->updateStaticTopPageData();

        return view('admin/admin_message_page', ['title' => 'updateStaticTopPageData done', 'message' => 'updateStaticTopPageData done']);
    }

    private function killmerge(SyncOpenChatState $json)
    {
        if ($json->isActive ?? false) {
            OpenChatApiDbMerger::enableKillFlag();

            return view('admin/admin_message_page', ['title' => 'OpenChatApiDbMerger', 'message' => 'OpenChatApiDbMergerを強制終了しました']);
        } else {
            echo 'fails';
        }
    }

    function phpinfo()
    {
        phpinfo();
    }
}
