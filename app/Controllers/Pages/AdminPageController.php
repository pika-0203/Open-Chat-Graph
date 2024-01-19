<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

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
use App\Models\SQLite\SQLiteStatistics;
use App\Services\CronJson\SyncOpenChatState;
use App\Services\OpenChat\Store\OpenChatImageDeleter;
use App\Services\SitemapGenerator;
use App\Services\OpenChat\Updater\Process\OpenChatCrawlingProcess;
use App\Services\OpenChat\Updater\OpenChatUpdaterFromApi;
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
        $message .= 'syncOpenChatArchive: ' . $sql->syncOpenChatArchive(true) . "\nend: " . date('Y-m-d H:i:s') . "\n\n";
        DBGce::execute("TRUNCATE TABLE open_chat_merged");
        $message .= 'syncOpenChatMerged: ' . $sql->syncOpenChatMerged() . "\nend: " . date('Y-m-d H:i:s') . "\n\n";
        DBGce::execute("TRUNCATE TABLE user_registration_open_chat");
        $message .= 'syncUserRegistrationOpenChat: ' . $sql->syncUserRegistrationOpenChat() . "\nend: " . date('Y-m-d H:i:s') . "\n\n";
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

    private function removedeleted(OpenChatImageDeleter $openChatImageDeleter)
    {
        set_time_limit(3600);

        $openChat = (DB::fetchAll('SELECT id, img_url, url FROM open_chat WHERE is_alive = 0'));

        foreach ($openChat as $oc) {
            $openChatImageDeleter->deleteImage($oc['id'], $oc['img_url']);
            DB::execute('DELETE FROM open_chat WHERE id = ' . $oc['id']);
            DB::execute('DELETE FROM open_chat_deleted WHERE id = ' . $oc['id']);
        }

        echo "done " . count($openChat);
    }

    private function removeonlyroom(OpenChatImageDeleter $openChatImageDeleter)
    {
        set_time_limit(3600);

        $openChat = (DB::fetchAll('SELECT id, img_url, url FROM open_chat WHERE member < 3'));

        foreach ($openChat as $oc) {
            $openChatImageDeleter->deleteImage($oc['id'], $oc['img_url']);
            DB::execute('DELETE FROM open_chat WHERE id = ' . $oc['id']);
            DB::execute('DELETE FROM open_chat_deleted WHERE id = ' . $oc['id']);
        }

        echo "done " . count($openChat);
    }

    private function recoveryimg(OpenChatImageStore $imgStore, OpenChatCrawler $crawler)
    {
        set_time_limit(3600);

        $rootDir = __DIR__ . '/../../../public/oc-img/';
        $getDir = fn (int $id): string => $rootDir . (string)floor($id / 1000) . '/';

        $openChat = (DB::fetchAll('SELECT id, img_url, url FROM open_chat WHERE is_alive = 1'));

        $count = 0;
        foreach ($openChat as $oc) {
            if ($oc['img_url'] === 'noimage') {
                $dto = $crawler->fetchOpenChatDto($oc['url']);

                if (!$dto) {
                    pre_var_dump('failed fetch open chat: ' . $oc['id']);
                }

                $count++;
                pre_var_dump($oc);
                pre_var_dump($imgStore->downloadAndStoreOpenChatImage($dto->profileImageObsHash, $oc['id']));

                continue;
            }

            $filePath = $getDir($oc['id']) . $oc['img_url'] . '.webp';

            if (!file_exists($filePath)) {
                $count++;
                pre_var_dump($oc);
                pre_var_dump($imgStore->downloadAndStoreOpenChatImage($oc['img_url'], $oc['id']));
            }
        }

        echo "done {$count}";
    }

    private function recoveryimg2()
    {
        set_time_limit(3600);

        $rootDir = __DIR__ . '/../../../public/oc-img/';
        $getDir = fn (int $id): string => (string)floor($id / 1000) . '/';

        $recovery = function ($openChat) use ($getDir, $rootDir) {
            foreach ($openChat as $oc) {
                $filePath = $rootDir . $getDir($oc['id']) . 'preview/' . $oc['img_url'] . '_p.webp';
                //$filePath = $rootDir . $getDir($oc['id']) . $oc['img_url'] . '.webp';
                if (file_exists($filePath)) {
                    continue;
                }

                $url = 'http://openchat-review.website/oc-img/' . $getDir($oc['id']) . 'preview/' . $oc['img_url'] . '_p.webp';
                //$url = 'http://openchat-review.website/oc-img/' . $getDir($oc['id']) . $oc['img_url'] . '.webp';

                try {
                    $data = file_get_contents($url);
                    if (!$data) {
                        pre_var_dump('false: ' . $url);
                    } else {
                        file_put_contents($filePath, $data);
                        pre_var_dump('done: ' . $url);
                    }
                } catch (\Throwable $e) {
                    pre_var_dump($e->getMessage());
                }
            }
        };

        //$openChat = (DB::fetchAll('SELECT id, img_url FROM open_chat'));
        $openChat2 = (DB::fetchAll('SELECT id, img_url FROM open_chat_archive'));

        $recovery($openChat2);
    }

    function phpinfo()
    {
        phpinfo();
    }
}
