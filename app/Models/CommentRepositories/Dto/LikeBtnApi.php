<?php

namespace App\Models\CommentRepositories\Dto;

class LikeBtnApi
{
    public int $empathyCount = 0;
    public int $insightsCount = 0;
    public int $negativeCount = 0;
    public string $voted = '';
}
