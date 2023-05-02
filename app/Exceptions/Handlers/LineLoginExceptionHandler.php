<?php

namespace App\Exceptions\Handlers;

use App\Models\Repositories\LogRepositoryInterface;

class LineLoginExceptionHandler implements AppExceptionHandlerInterface
{
    private LogRepositoryInterface $logRepository;

    function __construct(LogRepositoryInterface $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    public function handleException(\Throwable $e)
    {
        $this->logRepository->logLoginError(0, getIP(), getUA(), $e->getMessage());
        session()->addError(get_class($e), $e->getCode(), $e->getMessage());
        redirect(session('return_to'))->send();
    }
}
