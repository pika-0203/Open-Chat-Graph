<?php

declare(strict_types=1);

namespace App\Models\Accreditation;

use App\Services\Accreditation\Dto\QuizApiQuestionDto;
use App\Services\Accreditation\Enum\ExamType;

class QuizApiModel
{
    /**
     *  @return QuizApiQuestionDto[]
     */
    function getQuizApiQuestionDto(ExamType $type): array
    {
        $query =
            "SELECT
                t1.id,
                t1.question,
                t1.answers,
                t1.explanation,
                t2.name AS user_name,
                t2.room_name,
                t2.url AS room_url
            FROM
                exam AS t1
                JOIN user AS t2 ON t1.user_id = t2.id
            WHERE
                type = :type
                AND publishing = 1";

        $type = $type->value;

        return AccreditationDB::fetchAll(
            $query,
            compact('type'),
            [\PDO::FETCH_CLASS, QuizApiQuestionDto::class]
        );
    }
}
