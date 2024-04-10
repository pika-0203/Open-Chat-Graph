<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

class OpenChatUpdaterDtoFactory
{
    function mapToDto(OpenChatRepositoryDto $repoDto, OpenChatDto $apiDto, bool $updateMember): OpenChatUpdaterDto
    {
        $updaterDto = new OpenChatUpdaterDto($repoDto->open_chat_id);

        foreach ($repoDto as $prop => $value) {
            if ($prop === 'open_chat_id') {
                continue;
            }

            if ($prop === 'memberCount' && !$updateMember) {
                $updaterDto->memberCount = null;
                continue;
            }

            if (!isset($apiDto->$prop) || $apiDto->$prop === $value) {
                $updaterDto->$prop = null;
            }

            $updaterDto->$prop = $apiDto->$prop;
        }

        if (
            $updaterDto->name !== null
            || $updaterDto->desc !== null
        ) {
            $updaterDto->rewriteUpdateAtTime();
        }

        return $updaterDto;
    }

    function mapToDeleteOpenChatDto(int $open_chat_id): OpenChatUpdaterDto
    {
        $updaterDto = new OpenChatUpdaterDto($open_chat_id);

        $updaterDto->delete_flag = true;

        return $updaterDto;
    }
}
