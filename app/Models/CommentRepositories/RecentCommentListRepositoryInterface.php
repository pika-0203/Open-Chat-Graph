<?php

namespace App\Models\CommentRepositories;

interface RecentCommentListRepositoryInterface
{
    /**
     * @return array{ id:int,name:string,img_url:string,description:string,member:int,emblem:int,time:string }[]
     */
    public function findRecentCommentOpenChat(int $offset, int $limit): array;
    /**
     * @return array{ id:int,name:string,img_url:string,description:string,member:int,emblem:int,time:string }[]
     */
    public function findRecentCommentOpenChatAll(int $offset, int $limit, string $adminId = ''): array;
}
