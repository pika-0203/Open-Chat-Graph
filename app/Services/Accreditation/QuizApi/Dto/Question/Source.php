<?php

declare(strict_types=1);

namespace App\Services\Accreditation\QuizApi\Dto\Question;

class Source
{
    public function __construct(
        public string $title,
        public string $url,
    ) {
    }
}
