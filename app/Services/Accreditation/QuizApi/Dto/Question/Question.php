<?php

declare(strict_types=1);

namespace App\Services\Accreditation\QuizApi\Dto\Question;

class Question
{
    public ?string $image;
    public string $type = 'MCQs';

    /**
     * @param string[] $choices
     */
    public function __construct(
        public string $question,
        public array $choices,
        public array $correctAnswers,
        public int $score,
        public Contributor $contributor,
        public string $explanation,
        public Source $source,
        public int $id,
    ) {
    }
}
