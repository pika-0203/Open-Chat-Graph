<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater\Process;

use App\Services\OpenChat\Crawler\OpenChatApiFromEmidDownloader;
use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
use App\Services\OpenChat\Updater\OpenChatUpdaterInterface;
use App\Services\OpenChat\Registration\OpenChatFromApiRegistration;
use App\Models\Repositories\OpenChatRepositoryWithCacheForUpdater;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use Shadow\DB;

class OpenChatApiDbMergerProcess
{
    private OpenChatApiFromEmidDownloader $openChatApiOcDataFromEmidDownloader;
    private OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatDataWithCache;
    private OpenChatUpdaterInterface $openChatUpdater;
    private OpenChatFromApiRegistration $openChatFromApiRegistration;

    function __construct(
        OpenChatApiFromEmidDownloader $openChatApiOcDataFromEmidDownloader,
        OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatDataWithCache,
        OpenChatUpdaterInterface $openChatUpdater,
        OpenChatRepositoryWithCacheForUpdater $openChatRepository,
    ) {
        $this->openChatApiOcDataFromEmidDownloader = $openChatApiOcDataFromEmidDownloader;
        $this->openChatDataWithCache = $openChatDataWithCache;
        $this->openChatUpdater = $openChatUpdater;
        $this->openChatFromApiRegistration = app(OpenChatFromApiRegistration::class, compact('openChatRepository'));
    }

    function validateAndMapToOpenChatDtoCallback(OpenChatDto $apiDto, bool $updateFlag = true): ?string
    {
        // Emidが一致するオープンチャットを取得する
        $openChatByEmid = $this->openChatDataWithCache->getOpenChatIdByEmid($apiDto->emid);
        if ($openChatByEmid && (!$openChatByEmid['next_update'] || !$updateFlag)) {
            // 一致したデータがあり更新対象ではない場合
            return null;
        } elseif ($openChatByEmid) {
            // DBに一致するオープンチャットがある場合
            $this->openChatUpdater->updateOpenChat($openChatByEmid['id'], $apiDto);

            return null;
        }

        // 再接続
        if(!$updateFlag) {
            DB::$pdo = null;
        }

        // APIから追加のデータをダウンロードする
        $ocApiElement = $this->openChatApiOcDataFromEmidDownloader->fetchOpenChatApiFromEmidDtoElement($apiDto->emid);
        if (is_string($ocApiElement)) {
            // エラー文字列の場合
            return $ocApiElement;
        }

        // DTOに要素を追加する
        $apiDto->setOpenChatApiFromEmidDtoElement($ocApiElement);

        // 一致するオープンチャットが無い場合、別の情報に一致するEmidを持たないオープンチャットを探す
        $existingOpenChatId = $this->openChatDataWithCache->findDuplicateOpenChat($apiDto);
        // 一致するオープンチャットがある場合
        if ($existingOpenChatId) {
            $this->openChatUpdater->updateOpenChat($existingOpenChatId, $apiDto);

            return null;
        }

        // 収集拒否の場合
        if (OpenChatServicesUtility::containsHashtagNolog($apiDto->desc)) {
            return null;
        }

        // 一致するオープンチャットが無い場合、新しく登録する
        $this->openChatFromApiRegistration->registerOpenChatFromApi($apiDto);

        return null;
    }
}
