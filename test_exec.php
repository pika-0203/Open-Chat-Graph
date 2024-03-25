<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Services\OpenChat\Store\OpenChatImageStore;
use Shadow\DB;

/**
 * @var OpenChatImageStore $img
 */
$img = app(OpenChatImageStore::class);
try {
    AdminTool::sendLineNofity('start');

    $ocs = DB::fetchAll("SELECT id, img_url FROM open_chat");
    foreach ($ocs as $i => $oc) {
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
