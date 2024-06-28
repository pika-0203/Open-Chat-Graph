<?php

declare(strict_types=1);

namespace App\Services\Accreditation\Recommend;

use App\Services\Accreditation\Dto\QuestionDto;
use App\Services\Accreditation\Recommend\StaticData\AccreditationStaticDataFile;

class AcrreditationRecommend
{
    /**
     * @var QuestionDto[]
     */
    public array $questions;

    function __construct(
        AccreditationStaticDataFile $accreditationStaticDataFile
    ) {
        $this->questions = $accreditationStaticDataFile->getQuestions();
    }

    /**
     * @return QuestionDto[]
     */
    function getRandomQuestions(int $lengh): array
    {
        $shuffled = $this->questions;
        shuffle($shuffled);
        return array_slice($shuffled, 0, $lengh);
    }
}
