<?php

declare(strict_types=1);

namespace App\Models\CommentRepositories;

use App\Models\CommentRepositories\Dto\CommentPostApiArgs;

class CommentPostRepository implements CommentPostRepositoryInterface
{
    function addComment(CommentPostApiArgs $args): int
    {
        $query =
            "INSERT INTO
                comment (open_chat_id, id, user_id, name, text)
            SELECT
                :open_chat_id,
                (
                    SELECT
                        IFNULL(MAX(id), 0) + 1
                    FROM
                        comment
                    WHERE
                        open_chat_id = :open_chat_id
                ),
                :user_id,
                :name,
                :text";

        return CommentDB::executeAndGetLastInsertId($query, [
            'open_chat_id' => $args->open_chat_id,
            'user_id' => $args->user_id,
            'name' => $args->name,
            'text' => $args->text,
        ]);
    }
}
