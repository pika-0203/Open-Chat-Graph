<?php

declare(strict_types=1);

namespace App\Models\Accreditation;

use App\Services\Accreditation\Dto\QuestionDto;

class AccreditationHomePageModel
{
    /**
     * @return array{ total_count_gold:int,total_count_silver:int,total_count_bronze:int,publishing_count_gold:int,publishing_count_silver:int,publishing_count_bronze:int }
     */
    function getQuestionCount(): array
    {
        $sql =
            "SELECT 
                SUM(CASE WHEN type = 'gold' THEN 1 ELSE 0 END) as total_count_gold,
                SUM(CASE WHEN type = 'silver' THEN 1 ELSE 0 END) as total_count_silver,
                SUM(CASE WHEN type = 'bronze' THEN 1 ELSE 0 END) as total_count_bronze,
                SUM(CASE WHEN type = 'gold' AND publishing = 1 THEN 1 ELSE 0 END) as publishing_count_gold,
                SUM(CASE WHEN type = 'silver' AND publishing = 1 THEN 1 ELSE 0 END) as publishing_count_silver,
                SUM(CASE WHEN type = 'bronze' AND publishing = 1 THEN 1 ELSE 0 END) as publishing_count_bronze
            FROM 
                exam";

        return AccreditationDB::fetch($sql);
    }

    /**
     * @return QuestionDto[]
     */
    function getQuestionList(int $limit): array
    {
        return AccreditationDB::fetchAll(
            AccreditationUserModel::getQuestionQuery() . "ORDER BY t1.created_at DESC LIMIT :limit",
            compact('limit'),
            [\PDO::FETCH_CLASS, QuestionDto::class]
        );
    }
}
