<?php

declare(strict_types=1);

namespace App\Models\CommentRepositories\Dto;

use App\Config\AdminConfig;

class CommentListApi
{
    public int $id;
    public int $commentId;
    public string $name;
    public string $text;
    public string $time;
    public string $userId;
    public int $empathyCount;
    public int $insightsCount;
    public int $negativeCount;
    public string $voted;

    function getResponseArray(): array
    {
        return [
            'comment' => [
                'id' => $this->id,
                'commentId' => $this->commentId,
                'name' => $this->name,
                'text' => $this->text,
                'time' => $this->time,
                'userId' => $this->userId === AdminConfig::ADMIN_API_KEY ? '管理者' : ''
            ],
            'like' => [
                'empathyCount' => $this->empathyCount,
                'insightsCount' => $this->insightsCount,
                'negativeCount' => $this->negativeCount,
                'voted' => $this->voted,
            ]
        ];
    }
}
