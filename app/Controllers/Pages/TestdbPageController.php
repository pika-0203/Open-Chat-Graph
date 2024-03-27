<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use Shadow\DB;

class TestdbPageController
{
    function index(?string $token, ?int $page, ?int $limit)
    {
        if ($token !== 'inuinuINU1234' || !$page || !$limit) return false;

        $offset = ($page - 1) * $limit;
        $id = DB::fetchAll("SELECT id FROM open_chat LIMIT {$offset}, {$limit}");
        $id ? response($id) : false;
    }
}
