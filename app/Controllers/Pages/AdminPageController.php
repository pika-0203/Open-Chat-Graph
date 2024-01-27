<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\UpdateRankingService;
use App\Services\StaticData\StaticTopPageDataGenerator;
use App\Services\Admin\AdminAuthService;
use Shadow\DB;
use App\Services\Admin\AdminTool;
use App\Services\OpenChat\DuplicateOpenChatMeger;
use App\Services\OpenChat\OpenChatApiDbMerger;
use App\Models\GCE\GceDbTableSynchronizer;
use App\Models\GCE\DBGce;
use App\Models\GCE\GceRankingUpdater;
use App\Models\Repositories\DeleteOpenChatRepositoryInterface;
use App\Services\GceDifferenceUpdater;
use App\Models\SQLite\SQLiteStatistics;
use App\Services\CronJson\SyncOpenChatState;
use Shared\Exceptions\NotFoundException;

class AdminPageController
{
    function __construct(AdminAuthService $adminAuthService)
    {
        if (!$adminAuthService->auth()) {
            throw new NotFoundException;
        }
    }

    function index()
    {
        throw new \Exception('test');
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

    private function gcelatest(GceDifferenceUpdater $gce, DuplicateOpenChatMeger $dupMeger)
    {
        $gce->finalizeSyncLatest();
        $result = $dupMeger->mergeDuplicateOpenChat();
        $gce->finalizeOpenChatMerged();

        echo 'done';
    }

    function gcesyncall(GceDbTableSynchronizer $sql, GceRankingUpdater $gce, GceDifferenceUpdater $gcedeiff)
    {
        set_time_limit(3600 * 3);
        $message = "start: " . date('Y-m-d H:i:s') . "\n\n";

        //$gcedeiff->finalizeDifferenceUpdate();

        DBGce::execute("TRUNCATE TABLE open_chat");
        $message .= 'syncOpenChatAll: ' . $sql->syncOpenChatAll() . "\nend: " . date('Y-m-d H:i:s') . "\n\n";
        AdminTool::sendLineNofity($message);

        DBGce::execute("TRUNCATE TABLE open_chat_archive");
        DBGce::execute("TRUNCATE TABLE open_chat_merged");
        $message .= 'syncOpenChatMerged: ' . $sql->syncOpenChatMerged() . "\nend: " . date('Y-m-d H:i:s') . "\n\n";
        DBGce::execute("TRUNCATE TABLE user_registration_open_chat");
        $gce->updateRanking();

        return view('admin/admin_message_page', ['title' => 'GCE SQL 同期完了', 'message' => $message]);
    }

    private function gcegenerank(GceRankingUpdater $gce)
    {
        $gce->updateRanking();
        echo 'done';
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

    function killmerge(SyncOpenChatState $json)
    {
        if ($json->isActive ?? false) {
            OpenChatApiDbMerger::enableKillFlag();

            return view('admin/admin_message_page', ['title' => 'OpenChatApiDbMerger', 'message' => 'OpenChatApiDbMergerを強制終了しました']);
        } else {
            echo 'fails';
        }
    }

    function removedeleted(DeleteOpenChatRepositoryInterface $dRepo)
    {
        set_time_limit(3600);

        $openChat = (DB::fetchAll("SELECT id FROM open_chat WHERE is_alive = 0 OR emid IS NULL OR emid = ''"));

        foreach ($openChat as $oc) {
            $dRepo->deleteOpenChat($oc['id']);
        }

        echo "done " . count($openChat);
    }

    function phpinfo()
    {
        phpinfo();
    }
}
