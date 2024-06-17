<?php

declare(strict_types=1);

namespace App\Models\Accreditation;

use App\Services\Accreditation\Dto\QuestionDto;
use App\Services\Accreditation\Enum\ExamType;
use DateTime;

class AccreditationUserModel
{
    /**
     * @return array{ id:int,user_id:string,name:string,url:string,room_name:string,is_admin:int }|false
     */
    function getProfile(string $user_id): array|false
    {
        return AccreditationDB::fetch(
            "SELECT * FROM user WHERE user_id = :user_id",
            compact('user_id')
        );
    }

    /**
     * @return array{ id:int,user_id:string,name:string,url:string,room_name:string,is_admin:int }|false
     */
    function getProfileById(int $id): array|false
    {
        return AccreditationDB::fetch(
            "SELECT * FROM user WHERE id = :id",
            compact('id')
        );
    }

    function getUserIncrementId(string $user_id): int|false
    {
        return AccreditationDB::fetchColumn(
            "SELECT id FROM user WHERE user_id = :user_id",
            compact('user_id')
        );
    }

    private function userLog(int $user_increment_id, string $ip, string $ua, string $type)
    {
        if ((strpos($ua, 'Android') !== false)) {
            $ua = 'Android';
        } elseif ((strpos($ua, 'iPhone') !== false)) {
            $ua = 'iPhone';
        } elseif ((strpos($ua, 'iPad') !== false)) {
            $ua = 'iPad';
        } elseif ((strpos($ua, 'Macintosh') !== false)) {
            $ua = 'Macintosh';
        } elseif ((strpos($ua, 'Windows') !== false)) {
            $ua = 'Windows';
        } elseif ((strpos($ua, 'Linux') !== false)) {
            $ua = 'Linux';
        } else {
            echo "Other";
        }

        AccreditationDB::execute(
            "INSERT INTO user_log(user_increment_id, ip, ua, type) VALUES(:user_increment_id, :ip, :ua, :type)",
            compact('user_increment_id', 'ip', 'ua', 'type')
        );
    }

    /**
     * @param array{ user_id:string,name:string,url:string,room_name:string,is_admin:int } $params
     */
    function registerProfile(array $params, string $ip, string $ua): bool
    {
        $user_id = $params['user_id'];
        $user_increment_id = AccreditationDB::fetchColumn("SELECT id FROM user WHERE user_id = :user_id", ['user_id' => $user_id]);

        if ($user_increment_id) {
            $this->userLog($user_increment_id, $ip, $ua, 'updateProfile');

            return AccreditationDB::executeAndCheckResult(
                "UPDATE user 
                SET name = :name, url = :url, room_name = :room_name, is_admin = :is_admin
                WHERE user_id = :user_id",
                $params
            );
        } else {
            $user_increment_id = AccreditationDB::executeAndGetLastInsertId(
                "INSERT INTO user (user_id, name, url, room_name, is_admin) 
                VALUES (:user_id, :name, :url, :room_name, :is_admin)",
                $params
            );

            $this->userLog($user_increment_id, $ip, $ua, 'registerProfile');
            return !!$user_increment_id;
        }
    }

    private function getQuestionQuery()
    {
        return
            "SELECT
                t1.*,
                t2.name AS user_name,
                IFNULL(t2.is_admin, 0) AS is_admin_user,
                IFNULL(t3.name, '') AS edit_user_name,
                IFNULL(t3.is_admin, 0) AS is_admin_edit_user
            FROM
                exam AS t1
                JOIN user AS t2 ON t1.user_id = t2.id
                LEFT JOIN user AS t3 ON t1.edit_user_id = t3.id
            ";
    }

    /**
     * @return QuestionDto|false
     */
    function getQuestionById(int $id): QuestionDto|false
    {
        return AccreditationDB::fetch(
            $this->getQuestionQuery() . "WHERE t1.id = :id",
            compact('id'),
            [\PDO::FETCH_CLASS, QuestionDto::class]
        );
    }

    /**
     * @return QuestionDto[]
     */
    function getQuestionList(int $publishing, ExamType $type): array
    {
        return AccreditationDB::fetchAll(
            $this->getQuestionQuery() . "WHERE t1.publishing = :publishing ORDER BY t1.edited_at DESC",
            compact('publishing'),
            [\PDO::FETCH_CLASS, QuestionDto::class]
        );
    }

    /**
     * @return QuestionDto[]
     */
    function getMyQuestionList(int $user_id, ExamType $type): array
    {
        return AccreditationDB::fetchAll(
            $this->getQuestionQuery() . "WHERE t1.user_id = :user_id ORDER BY t1.edited_at DESC",
            compact('user_id'),
            [\PDO::FETCH_CLASS, QuestionDto::class]
        );
    }

    /**
     * @param array{ question:string,answers:array{ a:string,b:stirng,c:string,d:stirng,correct:stirng },explanation:array{ explanation:string,source_url:string,sorce_title:string },user_id:int,publishing:int,type:ExamType } $params
     */
    function registerQuestion(array $params, string $ip, string $ua): bool
    {
        $params['answers'] = json_encode($params['answers']);
        $params['explanation'] = json_encode($params['explanation']);
        $params['type'] = $params['type']->value;

        $this->userLog($params['user_id'], $ip, $ua, 'registerQuestion');

        return AccreditationDB::executeAndCheckResult(
            "INSERT INTO exam (question, answers, explanation, user_id, publishing, type) 
                VALUES (:question, :answers, :explanation, :user_id, :publishing, :type)",
            $params
        );
    }

    /**
     * @param array{ id:int,question:string,answers:array{ a:string,b:stirng,c:string,d:stirng,correct:stirng },explanation:array{ explanation:string,source_url:string,sorce_title:string },edit_user_id:int,publishing:int,type:ExamType } $params
     */
    function updateQuestion(array $params, string $ip, string $ua): bool
    {
        $params['answers'] = json_encode($params['answers']);
        $params['explanation'] = json_encode($params['explanation']);
        $params['type'] = $params['type']->value;
        $params['edited_at'] = (new DateTime())->format('Y-m-d H:i:s');

        $this->userLog($params['edit_user_id'], $ip, $ua, 'updateQuestion');

        return AccreditationDB::executeAndCheckResult(
            "UPDATE exam 
                SET question = :question, answers = :answers, explanation = :explanation, edit_user_id = :edit_user_id, edited_at = :edited_at, publishing = :publishing, type = :type
                WHERE id = :id",
            $params
        );
    }

    function deleteQuestion(int $id, int $user_id, string $ip, string $ua): bool
    {
        $this->userLog($user_id, $ip, $ua, 'deleteQuestion');

        return AccreditationDB::executeAndCheckResult(
            "DELETE FROM exam WHERE id = :id",
            compact('id')
        );
    }
}
