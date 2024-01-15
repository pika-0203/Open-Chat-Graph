<?php

declare(strict_types=1);

namespace App\Services\OpenChat\Dto;

class ArchiveFlagsDto
{
    public int $open_chat_id;
    public bool $update_name;
    public bool $update_img;
    public bool $update_description;

    function __construct(int $open_chat_id, bool $update_name, bool $update_img, bool $update_description)
    {
        $this->open_chat_id = $open_chat_id;
        $this->update_name = $update_name;
        $this->update_img = $update_img;
        $this->update_description = $update_description;
    }

    function toArray(): array
    {
        return get_object_vars($this);
    }
}
