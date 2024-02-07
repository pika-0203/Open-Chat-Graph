<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatDto;

class OpenChatRepositoryWithCacheForUpdater extends OpenChatRepository implements OpenChatRepositoryInterface
{
    public function addOpenChatFromDto(OpenChatDto $dto): int|false
    {
        $id = parent::addOpenChatFromDto($dto);
        if (!$id) {
            return false;
        }

        OpenChatDataForUpdaterWithCacheRepository::addOpenChatIdByEmidCache($id, $dto->emid, $dto->profileImageObsHash);
        return $id;
    }
}
