<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater\Process;

use App\Services\OpenChat\Crawler\OpenChatApiFromEmidDownloader;
use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
use App\Services\OpenChat\Updater\OpenChatUpdater;
use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use Shadow\DB;

class OpenChatApiDbMergerProcess
{
    function __construct(
        private OpenChatApiFromEmidDownloader $openChatApiOcDataFromEmidDownloader,
        private OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatDataWithCache,
        private OpenChatUpdater $openChatUpdater,
        private OpenChatRepositoryInterface $openChatRepository,
    ) {
    }

    function validateAndMapToOpenChatDtoCallback(OpenChatDto $apiDto, bool $updateFlag = true): ?string
    {
        // Emidが一致するオープンチャットを取得する
        $openChatByEmid = $this->openChatDataWithCache->getOpenChatIdByEmid($apiDto->emid);

        if ($updateFlag) {
            if ($openChatByEmid && !$openChatByEmid['next_update']) {
                // 一致したデータがあり更新対象ではない場合
                return null;
            }
        } else {
            // 再接続
            DB::$pdo = null;

            if ($openChatByEmid && $openChatByEmid['img_url'] === $apiDto->profileImageObsHash) {
                // 一致したデータがあり更新対象ではない場合
                return null;
            }
        }

        if ($openChatByEmid) {
            // DBに一致するオープンチャットがある場合
            $this->openChatUpdater->updateOpenChat($openChatByEmid['id'], $apiDto);
            return null;
        }

        // 収集拒否の場合
        if (OpenChatServicesUtility::containsHashtagNolog($apiDto->desc)) {
            return null;
        }

        $this->openChatRepository->addOpenChatFromDto($apiDto);

        return null;
    }
}
