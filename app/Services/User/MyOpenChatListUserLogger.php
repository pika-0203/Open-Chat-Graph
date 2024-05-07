<?php

declare(strict_types=1);

namespace App\Services\User;

use App\Models\UserLogRepositories\UserLogRepository;

class MyOpenChatListUserLogger
{
    function __construct(
        private UserLogRepository $userLogRepository,
    ) {
    }

    function userMyListLog(string $userId, int $expires, array $idArray)
    {
        if (!$this->userLogRepository->checkExistsUserListLog($userId, $expires)) {
            $this->userLogRepository->insertUserListLog($idArray, $userId, $expires, getIP(), getUA());
        }

        $this->userLogRepository->insertUserListShowLog($userId);
    }
}
