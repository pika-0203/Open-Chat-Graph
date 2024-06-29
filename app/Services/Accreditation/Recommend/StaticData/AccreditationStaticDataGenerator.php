<?php

declare(strict_types=1);

namespace App\Services\Accreditation\Recommend\StaticData;

use App\Models\Accreditation\AccreditationUserModel;

class AccreditationStaticDataGenerator
{
    function __construct(
        private AccreditationUserModel $accreditationUserModel
    ) {
    }

    /**
     * @return QuestionDto[]
     */
    function getQuestions(): array
    {
        return $this->accreditationUserModel->getQuestionListAllWthiout();
    }

    private function updategetQuestions()
    {
        saveSerializedFile(
            "accreditation/cache/questions.dat",
            $this->getQuestions()
        );
    }

    function updateStaticData()
    {
        $this->updategetQuestions();
    }
}
