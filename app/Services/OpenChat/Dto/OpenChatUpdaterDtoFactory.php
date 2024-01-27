<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

class OpenChatUpdaterDtoFactory
{
    function mapToDto(int $open_chat_id, OpenChatRepositoryDto $repoDto, OpenChatDto $apiDto): OpenChatUpdaterDto
    {
        $updaterDto = new OpenChatUpdaterDto($open_chat_id);

        foreach ($repoDto as $prop => $value) {
            if (!isset($apiDto->$prop) || $apiDto->$prop === $value) {
                $updaterDto->$prop = null;
            } else {
                $updaterDto->$prop = $apiDto->$prop;
            }
        }

        $updaterDto->db_member = $repoDto->memberCount;
        $updaterDto->db_img_url = $repoDto->profileImageObsHash;
        $updaterDto->hasEmid = (bool)$repoDto->emid;

        return $updaterDto;
    }

    function mapToDeleteOpenChatDto(int $open_chat_id, string $imgUrl): OpenChatUpdaterDto
    {
        $updaterDto = new OpenChatUpdaterDto($open_chat_id);

        $updaterDto->delete_flag = true;
        $updaterDto->db_img_url = $imgUrl;

        return $updaterDto;
    }
}
