<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Dto\OpenChatRepositoryDto;
use App\Services\OpenChat\Dto\OpenChatUpdaterDto;

interface UpdateOpenChatRepositoryInterface
{
    /**
     * @return OpenChatRepositoryDto|false
     */
    public function getOpenChatDataById(int $id): OpenChatRepositoryDto|false;

    public function updateOpenChatRecord(OpenChatUpdaterDto $dto): void;

    public function getOpenChatIdByEmid(string $emid): int|false;


    /**
     * @param array{ open_chat_id: int, member: int } $oc
     */
    public function updateMemberColumn(array $oc): void;

    public function updateLocalImgUrl(int $open_chat_id, string $local_img_url): void;

    public function updateUrl(int $open_chat_id, string $url): void;

    /**
     * @return array{ id:int, img_url:string, local_img_url:string }[]
     */
    public function getUpdatedOpenChatBetweenUpdatedAt(\DateTime $start, \DateTime $end): array;

    /**
     * @param null|string $date Y-m-d
     * @return array{ id:int, img_url:string, local_img_url:string }[]
     */
    public function getOpenChatImgAll(?string $date = null): array;

    /**
     * @return array{ id:int, emid:string }[]
     */
    public function getEmptyUrlOpenChatId(): array;
}
