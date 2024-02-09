<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

class OpenChatUpdaterDto
{
    public int $updated_at;
    public int $open_chat_id;
    public bool $delete_flag = false;

    public ?string $emid = null;
    public ?string $name = null;
    public ?string $desc = null;
    public ?string $profileImageObsHash = null;
    public ?int $memberCount = null;
    public ?int $createdAt = null;
    public ?int $category = null;
    public ?int $emblem = null;

    public ?string $db_img_url = null;
    public ?int $db_member = null;

    function __construct(int $open_chat_id)
    {
        $this->updated_at = time();
        $this->open_chat_id = $open_chat_id;
    }
}
