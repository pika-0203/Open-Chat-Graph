<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;

sleep(5);
AdminTool::sendLineNofity(json_encode(json_decode($argv[1])));