<?php

namespace App\Services\Utility;

class ErrorCounter
{
    private int $continuousErrorsCount = 0;
    private int $maxContinuousErrors = 3;

    public function increaseCount()
    {
        $this->continuousErrorsCount++;
    }

    public function resetCount()
    {
        $this->continuousErrorsCount = 0;
    }

    public function hasExceededMaxErrors()
    {
        return $this->continuousErrorsCount > $this->maxContinuousErrors;
    }
}
