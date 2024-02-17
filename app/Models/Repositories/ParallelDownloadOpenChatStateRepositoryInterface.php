<?php

declare(strict_types=1);

namespace App\Models\Repositories;

use App\Services\OpenChat\Enum\RankingType;

interface ParallelDownloadOpenChatStateRepositoryInterface
{
    public function cleanUpAll(): void;

    public function isCompleted(RankingType $type, int $category): bool;

    public function updateComplete(RankingType $type, int $category): void;

    public function isDownloaded(RankingType $type, int $category): bool;

    public function updateDownloaded(RankingType $type, int $category): void;

    public function isCompletedAll(): bool;

    public function isDownloadedAll(): bool;
}
