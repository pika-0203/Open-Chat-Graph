<?php

declare(strict_types=1);

namespace App\Services\Accreditation;

use DateTime;
use Spatie\SchemaOrg\Schema;

class AccreditationSchemaGenerator
{
    // パンくずリスト
    static function breadcrumbList(
        string $secondName = '',
        string $secondPath = '',
        string $listItemName = 'オプチャ検定',
        string $path = 'accreditation',
    ): string {
        $breadcrumbList = Schema::breadcrumbList();

        $itemListElement = [
            Schema::listItem()
                ->position(1)
                ->name('トップ')
                ->item(rtrim(url(), '/')),
            Schema::listItem()
                ->position(2)
                ->name($listItemName)
                ->item(url($path)),
        ];

        if ($secondName && $secondPath) {
            $itemListElement[] = Schema::listItem()
                ->position(3)
                ->name($secondName)
                ->item(url("{$path}/{$secondPath}"));
        }

        $breadcrumbList->itemListElement($itemListElement);

        return $breadcrumbList->toScript();
    }

    static function singleQuiz(
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
