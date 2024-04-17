<?php

declare(strict_types=1);

namespace App\Services\OpenChatAdmin\Dto;

class AdminOpenChatDto
{
    public int $id;
    public string|false $recommendTag;
    public string|false $modifyTag;
}
