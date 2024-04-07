<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Services\OpenChat\Crawler\OpenChatApiFromEmidDownloader;
use App\Services\Recommend\RecommendUpdater;
use Shadow\DB;

set_time_limit(3600 * 10);

/**
 * @var RecommendUpdater $oc
 */
$oc = app(RecommendUpdater::class);
//$oc->updateRecommendTables(false);

/**
 * @var OpenChatApiFromEmidDownloader $api
 */
$api = app(OpenChatApiFromEmidDownloader::class);

try {
    AdminTool::sendLineNofity('oc start');

    $d = DB::fetchAll("SELECT id, emid FROM open_chat_deleted WHERE id > 96988 ORDER BY id ASC");
    foreach ($d as $oc) {
        try {
            $dto = $api->fetchOpenChatDto($oc['emid']);
        } catch (\App\Exceptions\InvalidMemberCountException $e) {
            continue;
        } catch (\Throwable $e) {
            AdminTool::sendLineNofity($e->__toString());
            AdminTool::sendLineNofity($oc['id']);
            exit;
        }

        if (!$dto) continue;
        DB::execute(
            "INSERT INTO recovery VALUES(:id, :name, :description)",
            ['id' => $oc['id'], 'name' => $dto->name, 'description' => $dto->desc]
        );
    }

    AdminTool::sendLineNofity('oc done');
} catch (\Throwable $e) {
    addCronLog($e->__toString());
    AdminTool::sendLineNofity($e->__toString());
}
