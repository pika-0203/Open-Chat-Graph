<?php

declare(strict_types=1);

namespace App\Services\Accreditation;

use DateTime;
use Spatie\SchemaOrg\Schema;

class SigleQuizPage
{
    static function generateSchema(
        string $headline,
        string $description,
        string $image,
        string $datePublished,
        string $dateModified,
        string $authorName,
        string $authorUrl,
        string $questionName,
        string $acceptedAnswerText,
        array $suggestedAnswers,
    ): string {
        return Schema::article()
            ->headline($headline)
            ->description($description)
            ->image($image)
            ->datePublished(new DateTime($datePublished))
            ->dateModified(new DateTime($dateModified))
            ->author(
                Schema::person()
                    ->name($authorName)
                    ->url($authorUrl)
            )
            ->mainEntity(
                Schema::question()
                    ->name($questionName)
                    ->acceptedAnswer(
                        Schema::answer()
                            ->text($acceptedAnswerText)
                    )
                    ->suggestedAnswer(
                        array_map(function ($answerText) {
                            return Schema::answer()->text($answerText);
                        }, $suggestedAnswers)
                    )
            )
            ->toScript();
    }
}
