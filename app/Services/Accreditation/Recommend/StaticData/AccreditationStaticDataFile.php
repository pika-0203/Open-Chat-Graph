<?php

declare(strict_types=1);

namespace App\Services\Accreditation\Recommend\StaticData;

class AccreditationStaticDataFile
{
    /**
     * @return QuestionDto[]
     */
    function getQuestions(): array
    {
        $data = getUnserializedFile("accreditation/cache/questions.dat");

        if (!$data) {
            /** @var AccreditationStaticDataGenerator $staticDataGenerator */
            $staticDataGenerator = app(AccreditationStaticDataGenerator::class);
            return $staticDataGenerator->getQuestions();
        }

        return $data;
    }
}
