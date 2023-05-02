<?php

namespace App\Exceptions\Handlers;

use App\Services\UserAuth\LoginSessionCookieManager;
use App\Models\Repositories\LogRepositoryInterface;

class InvalidTokenExceptionHandler implements AppExceptionHandlerInterface
{
    private LoginSessionCookieManager $sessionCookie;
    private LogRepositoryInterface $logRepository;

    function __construct(LoginSessionCookieManager $sessionCookie, LogRepositoryInterface $logRepository)
    {
        $this->sessionCookie = $sessionCookie;
        $this->logRepository = $logRepository;
    }

    public function handleException(\Throwable $e)
    {
        $this->sessionCookie->deleteSessionCookie();
        $this->logRepository->logLoginError(0, getIP(), getUA(), $e->getMessage());
        session()->addError(get_class($e), $e->getCode(), $e->getMessage());
        redirect()->send();
    }
}
