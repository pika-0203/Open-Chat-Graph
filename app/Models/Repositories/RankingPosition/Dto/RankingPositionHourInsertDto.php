<?php

declare(strict_types=1);

namespace App\Models\Repositories\RankingPosition\Dto;

class RankingPositionHourInsertDto
{
    public int $open_chat_id;
    public int $position;
    public int $category;
    public int $member;

    public function __construct(
        int $id,
        int $position,
        int $category,
        int $member
    ) {
        $this->open_chat_id = $id;
        $this->position = $position;
        $this->category = $category;
        $this->member = $member;
    }
}
