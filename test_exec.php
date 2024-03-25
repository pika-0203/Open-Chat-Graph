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

    $ocs = DB::fetchAll("SELECT id FROM open_chat ORDER BY id ASC");

    foreach ($ocs as $i => $oc) {
        OpenChatApiDbMerger::checkKillFlag();
        $id = $oc['id'];
        $img_url = DB::fetchColumn("SELECT img_url FROM open_chat WHERE id = {$id}");

        $result = $img->downloadAndStoreOpenChatImage($id, $img_url);
        if ($i % 10000 === 0) {
            AdminTool::sendLineNofity("key: {$i}");
        }

        $result && DB::execute(
            "UPDATE open_chat SET local_img_url = '{$result}' WHERE id = {$id}"
        );
    }

    AdminTool::sendLineNofity('done');
} catch (\Throwable $e) {
    addCronLog($e->__toString());
    AdminTool::sendLineNofity($e->__toString());
}
