<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

class ArchiveFlagsDtoFactory
{
    function generateArchiveFlagsDto(OpenChatUpdaterDto $updaterDto): ArchiveFlagsDto|false
    {
        if (!$updaterDto->hasEmid) {
            return false;
        }

        $flags = [
            $updaterDto->name !== null,
            $updaterDto->profileImageObsHash !== null,
            $updaterDto->desc !== null,
        ];
        
        if (!array_filter($flags)) {
            return false;
        }
        
        return new ArchiveFlagsDto($updaterDto->open_chat_id, ...$flags);
    }
}
