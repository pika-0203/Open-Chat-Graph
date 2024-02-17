<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Services\Admin\AdminTool;
use App\Services\Cron\ParallelDownloadOpenChat;
use App\Services\OpenChat\OpenChatApiDataParallelDownloader;

try {
    /**
     * @var array{ type: string, category: int }[] $args
     */
    $args = json_decode($argv[1] ?? '', true);
    if (!is_array($args) || !isset($args[0]['type'], $args[0]['category'])) {
        throw new RuntimeException('無効なexec引数: ' . (string)($argv[1] ?? 'UNDIFINED'));
    }

    /** @var ParallelDownloadOpenChat $inst */
    $inst = app(ParallelDownloadOpenChat::class);
    $inst->handle($args);
} catch (\Throwable $e) {
    // 全てのダウンロードプロセスを強制終了する
    OpenChatApiDataParallelDownloader::enableKillFlag();
    AdminTool::sendLineNofity($e->__toString());
    addCronLog($e->__toString());
}
