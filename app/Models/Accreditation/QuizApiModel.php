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
    function getQuizApiQuestionDto(ExamType $type, int $limit): array
    {
        $query =
            "SELECT
                t1.id,
                t1.question,
                t1.answers,
                t1.explanation,
                t2.name AS user_name,
                t2.room_name,
                t2.url AS room_url,
                t1.type
            FROM
                exam AS t1
                JOIN user AS t2 ON t1.user_id = t2.id
            WHERE
                type = :type
                AND publishing = 1
            ORDER BY
                RAND()
            LIMIT
                :limit";

        $type = $type->value;

        return AccreditationDB::fetchAll(
            $query,
            compact('type', 'limit'),
            [\PDO::FETCH_CLASS, QuizApiQuestionDto::class]
        );
    }

    /**
     *  @return QuizApiQuestionDto
     */
    function getQuizApiQuestionDtoById(int $id): QuizApiQuestionDto|false
    {
        $query =
            "SELECT
                t1.id,
                t1.question,
                t1.answers,
                t1.explanation,
                t2.name AS user_name,
                t2.room_name,
                t2.url AS room_url,
                t1.type
            FROM
                exam AS t1
                JOIN user AS t2 ON t1.user_id = t2.id
            WHERE
                t1.isPabulished = 1
                AND t1.id = :id";

        return AccreditationDB::fetch(
            $query,
            compact('id'),
            [\PDO::FETCH_CLASS, QuizApiQuestionDto::class]
        );
    }
}
