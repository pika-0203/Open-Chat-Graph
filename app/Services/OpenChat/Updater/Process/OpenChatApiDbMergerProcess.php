<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater\Process;

use App\Services\OpenChat\Crawler\OpenChatApiFromEmidDownloader;
use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
use App\Models\Repositories\OpenChatRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatDto;
use App\Services\OpenChat\Store\OpenChatImageStore;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;
use Shadow\DB;

class OpenChatApiDbMergerProcess
{
    function __construct(
        private OpenChatApiFromEmidDownloader $openChatApiOcDataFromEmidDownloader,
        private OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatDataWithCache,
        private OpenChatMargeUpdateProcess $openChatMargeUpdateProcess,
        private OpenChatRepositoryInterface $openChatRepository,
        private OpenChatImageStore $openChatImageStore,
    ) {
    }

    function validateAndMapToOpenChatDtoCallback(OpenChatDto $apiDto): ?string
    {
        // Emidが一致するオープンチャットを取得する
        $repoDto = $this->openChatDataWithCache->getOpenChatDataByEmid($apiDto->emid);

        // 一致するものがない場合は追加
        if (!$repoDto) {
            // 再接続して追加
            DB::$pdo = null;
            $this->add($apiDto);
            return null;
        }

        // 更新がないかを確認
        if (
            $repoDto->name === $apiDto->name
            && $repoDto->desc === $apiDto->desc
            && $repoDto->profileImageObsHash === $apiDto->profileImageObsHash
            && $repoDto->category === $apiDto->category
            && $repoDto->emblem === $apiDto->emblem
        ) {
            return null;
        }

        // 再接続して更新
        DB::$pdo = null;
        $this->openChatMargeUpdateProcess->mergeUpdateOpenChat($repoDto, $apiDto, false);

        return null;
    }

    private function add(OpenChatDto $apiDto): void
    {
        // 収集拒否の場合
        if (OpenChatServicesUtility::containsHashtagNolog($apiDto)) {
            return;
        }

        $this->openChatRepository->addOpenChatFromDto($apiDto);
    }
}
