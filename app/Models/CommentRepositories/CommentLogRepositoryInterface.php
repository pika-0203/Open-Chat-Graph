<?php

namespace App\Models\CommentRepositories;

use App\Models\CommentRepositories\Enum\CommentLogType;

interface CommentLogRepositoryInterface
{
    function addLog(int $entity_id, CommentLogType $type, string $ip, string $ua, string $data = ''): int;

    function findReportLog(int $entity_id, CommentLogType $type, string $data): bool;
}
