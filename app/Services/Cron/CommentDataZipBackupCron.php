<?php

declare(strict_types=1);

namespace App\Services\Cron;

use App\Config\AppConfig;
use App\Models\CommentRepositories\CommentListRepositoryInterface;
use App\Models\CommentRepositories\LikePostRepositoryInterface;
use App\Services\Cron\CronJson\CommentDataZipBackupCronState;

class CommentDataZipBackupCron
{
    function __construct(
        private ZipBackupCron $zipBackupCron,
        private CommentListRepositoryInterface $commentListRepository,
        private LikePostRepositoryInterface $likePostRepository,
    ) {
    }

    function saveBackup(): string
    {
        $commentFile = AppConfig::COMMENT_DATA_FILE_PATH . '/comment/comment.dat';
        $likeFile = AppConfig::COMMENT_DATA_FILE_PATH . '/like/like.dat';

        saveSerializedFile(
            $commentFile,
            $this->commentListRepository->getCommentsAll(),
            true
        );

        saveSerializedFile(
            $likeFile,
            $this->likePostRepository->getLikeAll(),
            true
        );

        return $this->zipBackupCron->ftpZipBackUp(app(CommentDataZipBackupCronState::class));
    }
}
