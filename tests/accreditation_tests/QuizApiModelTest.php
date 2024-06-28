<?php

declare(strict_types=1);

use App\Models\Accreditation\QuizApiModel;
use App\Services\Accreditation\Enum\ExamType;
use PHPUnit\Framework\TestCase;

class QuizApiModelTest extends TestCase
{
    private QuizApiModel $inst;

    public function test()
    {
        $this->inst = app(QuizApiModel::class);

        debug($this->inst->getQuizApiQuestionDto(ExamType::Bronze));

        $this->assertTrue(true);
    }
}
