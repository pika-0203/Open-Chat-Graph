<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatDto;

class OpenChatRepositoryWithCacheForUpdater extends OpenChatRepository implements OpenChatRepositoryInterface
{
    public function addOpenChatFromDto(OpenChatDto $dto): int
    {
        $id = parent::addOpenChatFromDto($dto);
        OpenChatDataForUpdaterWithCacheRepository::addOpenChatIdByEmidCache($id, $dto->emid);
        return $id;
    }
}
