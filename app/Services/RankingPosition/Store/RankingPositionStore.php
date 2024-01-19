<?php

declare(strict_types=1);

namespace App\Services\RankingPosition\Store;

use App\Config\AppConfig;

class RankingPositionStore extends AabstractRankingPositionStore
{
    protected string $filePath = AppConfig::OPEN_CHAT_RANKING_POSITION_DIR;
}
