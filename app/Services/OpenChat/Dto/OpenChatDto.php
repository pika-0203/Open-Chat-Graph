<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class OpenChatDto
{
    public string $name;                     // ranking-api data
    public string $desc;                     // ranking-api data
    public string $profileImageObsHash;      // ranking-api data
    public ?int $memberCount;                // ranking-api data
    public ?string $emid = null;             // ranking-api data
    public ?int $createdAt = null;           // ranking-api data
    public ?int $category = null;            // ranking-api data
    public ?int $emblem = null;              // ranking-api data
    public ?string $invitationTicket = null; // api data
    public int $registered_created_at;       // construct value
    public int $registered_open_chat_id;     // DB registered

    function __construct()
    {
        $this->registered_created_at = time();
    }

    /**
     * @return array{ open_chat_id: int, member: int, date: string }
     */
    function getStatisticsParams(): array
    {
        return [
            'open_chat_id' => $this->registered_open_chat_id,
            'member' => $this->memberCount,
            'date' => OpenChatServicesUtility::getModifiedCronTime($this->registered_created_at)->format('Y-m-d'),
        ];
    }
}
