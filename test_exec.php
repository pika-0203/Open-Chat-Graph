<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Services\OpenChat\OpenChatApiDbMerger;
use App\Services\OpenChat\Store\OpenChatImageStore;
use Shadow\DB;

set_time_limit(3600 * 10);

/**
 * @var OpenChatImageStore $img
 */
$img = app(OpenChatImageStore::class);
try {
    AdminTool::sendLineNofity('start');

    $ocs = DB::fetchAll("SELECT id, img_url FROM open_chat WHERE id > 16530");
    foreach ($ocs as $i => $oc) {
        OpenChatApiDbMerger::checkKillFlag();
        $img->downloadAndStoreOpenChatImage($oc['id'], $oc['img_url']);
        if ($i % 10000 === 0) {
            AdminTool::sendLineNofity("key: {$i}");
        }
    }

    AdminTool::sendLineNofity('done');
} catch (\Throwable $e) {
    addCronLog($e->__toString());
    AdminTool::sendLineNofity($e->__toString());
}
