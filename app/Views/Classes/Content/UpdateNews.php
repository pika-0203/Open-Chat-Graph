<?php

declare(strict_types=1);

namespace App\Views\Content;

class UpdateNews
{
    /** @var string[]|string[][] $body */
    function __construct(
        public \DateTime $date,
        public string $title,
        public array $body,
    ) {
    }
}
