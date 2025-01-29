<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Config\AppConfig;
use App\Services\Admin\AdminTool;
use App\Services\Recommend\StaticData\RecommendStaticDataGenerator;
use App\Services\StaticData\StaticDataGenerator;
use Shared\MimimalCmsConfig;

try {
    set_time_limit(3600);

    if (isset($argv[1]) && $argv[1]) {
        MimimalCmsConfig::$urlRoot = $argv[1];
    }

    /**
     * @var StaticDataGenerator $staticDataGenerator
     * @var RecommendStaticDataGenerator $recommendStaticDataGenerator
     */
    $staticDataGenerator = app(StaticDataGenerator::class);
    $recommendStaticDataGenerator = app(RecommendStaticDataGenerator::class);

    AdminTool::sendLineNofity('staticDataGenerator start');
    $staticDataGenerator->updateStaticData();
    AdminTool::sendLineNofity("staticDataGenerator done\nrecommendStaticDataGenerator start");
    $recommendStaticDataGenerator->updateStaticData();
    AdminTool::sendLineNofity('recommendStaticDataGenerator done');
    
    touch(AppConfig::getStorageFilePath('hourlyCronUpdatedAtDatetime'));
} catch (\Throwable $e) {
    addCronLog($e->__toString());
    AdminTool::sendLineNofity($e->__toString());
}
