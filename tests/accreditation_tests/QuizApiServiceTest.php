<?php

declare(strict_types=1);

use App\Services\Accreditation\Enum\ExamType;
use App\Services\Accreditation\QuizApi\QuizApiService;
use PHPUnit\Framework\TestCase;

class QuizApiServiceTest extends TestCase
{
    private QuizApiService $inst;

    public function test()
    {
        $this->inst = app(QuizApiService::class);

        debug($this->inst->getTopic(ExamType::Bronze, 10, 120));

        $this->assertTrue(true);
    }
}
