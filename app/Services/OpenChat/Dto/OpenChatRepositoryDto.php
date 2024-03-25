<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

class OpenChatRepositoryDto
{
    public int $open_chat_id;
    public string $emid;
    public string $name;
    public string $desc;
    public string $profileImageObsHash;
    public int $memberCount;
    public ?int $createdAt;
    public ?int $category;
    public ?int $emblem;
    public ?string $invitationTicket;
    private string $local_img_url;

    /**
     * @param array{ emid: string, name: string, description: string, img_url: string, local_img_url: string, member: string, api_created_at: int | null, category: int | null, emblem: int | null }[] $openChatData
     */
    function __construct(int $id, array $openChatData)
    {
        $this->open_chat_id = $id;
        $this->emid = $openChatData['emid'];
        $this->name = $openChatData['name'];
        $this->desc = $openChatData['description'];
        $this->profileImageObsHash = $openChatData['img_url'];
        $this->memberCount = $openChatData['member'];
        $this->createdAt = $openChatData['api_created_at'];
        $this->category = $openChatData['category'];
        $this->emblem = $openChatData['emblem'];
        $this->invitationTicket = $openChatData['url'];

        $this->local_img_url = $openChatData['local_img_url'];
    }

    function getLocalImgUrl(): string
    {
        return $this->local_img_url;
    }
}
