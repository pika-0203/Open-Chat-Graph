<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Updater;

use App\Models\Repositories\OpenChatDataForUpdaterWithCacheRepositoryInterface;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;

class MemberColumnUpdater
{
    function __construct(
        private UpdateOpenChatRepositoryInterface $updateRepository,
        private OpenChatDataForUpdaterWithCacheRepositoryInterface $openChatDataWithCache,
    ) {
    }

    /**
     * @param array{ open_chat_id: int, member: int }[] $data
     */
    function updateMemberColumn(array $data)
    {
        foreach ($data as $oc) {
            $dto = $this->openChatDataWithCache->getOpenChatDataById($oc['open_chat_id']);
            if (!$dto || $dto->memberCount === $oc['member']) {
                continue;
            }

            $this->updateRepository->updateMemberColumn($oc);
        }
    }
}
