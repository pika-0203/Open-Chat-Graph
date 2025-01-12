<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Config\SecretsConfig;
use App\Config\AppConfig;
use App\Services\Furigana\YahooFuriganaService;
use Shadow\Kernel\Reception;

class FuriganaApiController
{
    function index(
        YahooFuriganaService $yahooFuriganaService,
        ?string $json,
    ) {
        Reception::$isJson = true;

        if (!$json)
            return false;

        $strings = json_decode($json, true);
        if (!$json || !is_array($strings))
            return false;


        $hash = base62Hash(hash('md5', $json));
        $fileName = AppConfig::FURIGANA_CACHE_DIR . "/{$hash}.dat";
        $data = getUnserializedFile($fileName);

        if (!$data) {
            $data = $yahooFuriganaService->getFuriganaFromArray($strings, SecretsConfig::$yahooClientId, 2);
            saveSerializedFile($fileName, $data);
        }

        return response($data);
    }
}
