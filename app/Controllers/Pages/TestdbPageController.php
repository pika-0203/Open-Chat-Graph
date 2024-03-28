<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use Shadow\DB;
use Shadow\Kernel\Validator;

class TestdbPageController
{
    function index(?string $token, ?string $page, ?string $limit)
    {
        if ($token !== 'inuinuINU1234' || !Validator::num($page, min: 1) || !Validator::num($limit, min: 1)) return false;

        $offset = ($page - 1) * $limit;
        $id = DB::fetchAll("SELECT id FROM open_chat WHERE id BETWEEN 3606 AND 5512 AND member < 500 AND emblem = 0 ORDER BY id DESC LIMIT {$offset}, {$limit}", null, [\PDO::FETCH_COLUMN, 0]);
        return $id ? response($id) : false;
    }
}
