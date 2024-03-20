<?php

declare(strict_types=1);

namespace App\Models\CommentRepositories\Enum;

enum LikeBtnType: string
{
    case Empathy = 'empathy';
    case Insights = 'insights';
    case Negative = 'negative';
}