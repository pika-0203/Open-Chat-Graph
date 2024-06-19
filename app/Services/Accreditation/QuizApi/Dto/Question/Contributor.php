<?php

declare(strict_types=1);

namespace App\Services\Accreditation\QuizApi\Dto\Question;

class Contributor
{
    public function __construct(
        public string $name,
        public string $roomName,
        public string $url
    ) {
    }
}
