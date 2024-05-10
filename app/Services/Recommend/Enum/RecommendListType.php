<?php

declare(strict_types=1);

namespace App\Services\Recommend\Enum;

enum RecommendListType {
    case Category;
    case Tag;
    case Official;
}