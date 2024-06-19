<?php

declare(strict_types=1);

namespace App\Services\Accreditation\QuizApi\Dto;

use App\Services\Accreditation\QuizApi\Dto\Question\Question;

class Topic
{
    /** @param Question[] $questions */
    public function __construct(
        public string $topic,
        public int $totalQuestions,
        public int $totalScore,
        public int $totalTime,
        public array $questions,
    ) {
    }
}
