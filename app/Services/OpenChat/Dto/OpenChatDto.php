<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

class OpenChatDto
{
    public int $regestered_created_at;

    public string $name;
    public string $desc;
    public string $profileImageObsHash;
    public int $memberCount;

    public ?string $emid = null;
    public ?int $createdAt = null;
    public ?int $category = null;
    public ?int $emblem = null;
    public ?string $invitationTicket = null;

    public int $regestered_open_chat_id;

    function __construct()
    {
        $this->regestered_created_at = time();
    }

    function setOpenChatApiFromEmidDtoElement(array $ocApiElement)
    {
        $this->invitationTicket = $ocApiElement['invitationTicket'];
    }

    function getNextUpdate(): string
    {
        return date('Y-m-d', strtotime('1 day', $this->regestered_created_at));
    }

    /**
     * @return array `['open_chat_id' => int, 'member' => int, 'date' => string]`
     */
    function getStatisticsParams():array
    {
        return [
            'open_chat_id' => $this->regestered_open_chat_id,
            'member' => $this->memberCount,
            'date' => date('Y-m-d', $this->regestered_created_at),
        ];
    }
}
