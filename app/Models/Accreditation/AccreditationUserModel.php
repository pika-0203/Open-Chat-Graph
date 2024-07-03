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
     * @return array{ id:int,user_id:string,name:string,url:string,room_name:string,is_admin:int }[]
     */
    function getProfilesByType(?ExamType $type): array
    {
        $params = null;
        $where = '';

        if ($type) {
            $type = $type->value;
            $params = compact('type');
            $where = "AND type = :type";
        }

        return AccreditationDB::fetchAll(
            "SELECT
                t1.*
            FROM
                user AS t1
            WHERE
                EXISTS (
                    SELECT
                        user_id
                    FROM
                        exam
                    WHERE
                        user_id = t1.id
                        {$where}
                )
            ORDER BY
                t1.is_admin DESC,
                name COLLATE utf8mb4_bin;",
            $params
        );
    }

    /**
     * @return array{ id:int,user_id:string,name:string,url:string,room_name:string,is_admin:int }[]
     */
    function getProfilesAll(): array|false
    {
        return AccreditationDB::fetchAll(
            "SELECT
                t1.*
            FROM
                user AS t1
            ORDER BY
                t1.is_admin DESC,
                name COLLATE utf8mb4_bin;"
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
        } elseif ((strpos($ua, 'CrOS') !== false)) {
            $ua = 'CrOS';
        } elseif ((strpos($ua, 'Linux') !== false)) {
            $ua = 'Linux';
        } else {
            $ua = "Other";
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

    static function getQuestionQuery()
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
    function getQuestionById(int $id, ?ExamType $type = null): QuestionDto|false
    {
        $params = compact('id');
        $where = "WHERE t1.id = :id";

        if ($type) {
            $params['type'] = $type->value;
            $where .= " AND type = :type";
        }

        return AccreditationDB::fetch(
            $this->getQuestionQuery() . $where,
            $params,
            [\PDO::FETCH_CLASS, QuestionDto::class]
        );
    }

    /**
     * @return QuestionDto[]
     */
    function getQuestionList(int $publishing, ExamType $type): array
    {
        $type = $type->value;

        return AccreditationDB::fetchAll(
            $this->getQuestionQuery() . "WHERE t1.publishing = :publishing AND type = :type ORDER BY t1.edited_at DESC",
            compact('publishing', 'type'),
            [\PDO::FETCH_CLASS, QuestionDto::class]
        );
    }

    /**
     * @return QuestionDto[]
     */
    function getQuestionListAll(int $publishing = 1): array
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
    function getMyQuestionList(int $user_id, ?ExamType $type = null): array
    {
        $params = compact('user_id');
        $where = "WHERE t1.user_id = :user_id";

        if ($type) {
            $params['type'] = $type->value;
            $where .= " AND type = :type";
        }

        return AccreditationDB::fetchAll(
            $this->getQuestionQuery() . $where. " ORDER BY t1.id DESC",
            $params,
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

        $question_id = AccreditationDB::executeAndGetLastInsertId(
            "INSERT INTO exam (question, answers, explanation, user_id, publishing, type) 
                VALUES (:question, :answers, :explanation, :user_id, :publishing, :type)",
            $params
        );

        return !!$question_id;

        /* return AccreditationDB::executeAndCheckResult(
            "INSERT INTO answer (question_id) 
                VALUES (:question_id)",
            compact('question_id')
        ); */
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

        $pabulished = '';
        if ($params['publishing']) {
            $pabulished = ',isPabulished = :isPabulished';
            $params['isPabulished'] = 1;
        }


        $this->userLog($params['edit_user_id'], $ip, $ua, 'updateQuestion');

        return AccreditationDB::executeAndCheckResult(
            "UPDATE exam 
                SET question = :question, answers = :answers, explanation = :explanation, edit_user_id = :edit_user_id, edited_at = :edited_at, publishing = :publishing, type = :type {$pabulished}
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

    function changePermissionQuestion(int $id, int $edit_user_id, int $user_id, string $ip, string $ua): bool
    {
        $this->userLog($user_id, $ip, $ua, 'changePermissionQuestion');

        return AccreditationDB::executeAndCheckResult(
            "UPDATE exam SET edit_user_id = :edit_user_id WHERE id = :id",
            compact('id', 'edit_user_id')
        );
    }

    function moveQuestion(int $id, ExamType $type, int $user_id, string $ip, string $ua): bool
    {
        $this->userLog($user_id, $ip, $ua, 'moveQuestion');

        $type = $type->value;
        return AccreditationDB::executeAndCheckResult(
            "UPDATE exam SET type = :type WHERE id = :id",
            compact('id', 'type')
        );
    }

    function setAdminPermission(int $id, int $is_admin, int $user_id, string $ip, string $ua): bool
    {
        $this->userLog($user_id, $ip, $ua, 'setAdminPermission');

        return AccreditationDB::executeAndCheckResult(
            "UPDATE user SET is_admin = :is_admin WHERE id = :id",
            compact('id', 'is_admin')
        );
    }

    /**
     * @return array{ id: int, edited_at: string }[]
     */
    public function getSiteMapData(): array
    {
        return AccreditationDB::fetchAll("SELECT id, edited_at FROM exam");
    }

    /**
     * @return int[]
     */
    function getQuestionIds(): array
    {
        return AccreditationDB::fetchAll(
            "SELECT
                t1.id
            FROM
                exam AS t1
                JOIN user AS t2 ON t1.user_id = t2.id
            WHERE
                t1.publishing = 1",
            args: [\PDO::FETCH_COLUMN]
        );
    }

    function getExamTableAll(): array
    {
        return AccreditationDB::fetchAll("SELECT * FROM exam");
    }

    function getUserTableAll(): array
    {
        return AccreditationDB::fetchAll("SELECT * FROM user");
    }
}
