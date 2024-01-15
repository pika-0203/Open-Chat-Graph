<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater\Finalizer;

use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;

class OpenChatUpdaterDtoFinalizer
{
    private OpenChatDataForUpdaterWithCacheRepositoryInterface $updateRepository;

    function __construct(
        OpenChatDataForUpdaterWithCacheRepositoryInterface $updateRepository,

    ) {
        $this->updateRepository = $updateRepository;
    }

    function finalizeUpdaterDtoGeneration(OpenChatUpdaterDto $updaterDto): OpenChatUpdaterDto
    {
        // 過去一週間でメンバー数に動きがあるかどうかの確認
        if (!isset($updaterDto->memberCount) && !$this->updateRepository->getMemberChangeWithinLastWeek($updaterDto->open_chat_id)) {
            // 動きがない場合一週間後の更新にする
            $updaterDto->next_update = strtotime('7 day');
        } else {
            $updaterDto->next_update = strtotime('1 day');
        }

        return $updaterDto;
    }
}
