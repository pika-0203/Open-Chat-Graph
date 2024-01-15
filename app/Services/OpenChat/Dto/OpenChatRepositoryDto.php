<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

class OpenChatRepositoryDto
{
    public ?string $emid;
    public string $name;
    public string $desc;
    public string $profileImageObsHash;
    public int $memberCount;
    public ?int $createdAt;
    public ?int $category;
    public ?int $emblem;
    public ?string $invitationTicket;

    function __construct(array $openChatData)
    {
        $this->emid = $openChatData['emid'];
        $this->name = $openChatData['name'];
        $this->desc = $openChatData['description'];
        $this->profileImageObsHash = $openChatData['img_url'];
        $this->memberCount = $openChatData['member'];
        $this->createdAt = $openChatData['api_created_at'];
        $this->category = $openChatData['category'];
        $this->emblem = $openChatData['emblem'];
        $this->invitationTicket = $openChatData['url'];
    }
}
