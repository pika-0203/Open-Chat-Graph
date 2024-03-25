<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\Admin\AdminTool;

use App\Services\OpenChat\Updater\OpenChatImageStoreUpdater;
use Shadow\DB;

set_time_limit(3600 * 10);

/**
 * @var UpdateOpenChatRepositoryInterface $oc
 */
$oc = app(UpdateOpenChatRepositoryInterface::class);
/**
 * @var OpenChatImageStoreUpdater $img
 */
$img = app(OpenChatImageStoreUpdater::class);

try {
    AdminTool::sendLineNofity('oc start');
    foreach ($oc->getOpenChatImgAll(false) as $oc) {
        if (!file_exists(publicDir(getImgPath($oc['id'], $oc['local_img_url'])))) {
            $img->updateImage($oc['id'], $oc['img_url']);
        }
    }

    AdminTool::sendLineNofity('oc done');
} catch (\Throwable $e) {
    addCronLog($e->__toString());
    AdminTool::sendLineNofity($e->__toString());
}
