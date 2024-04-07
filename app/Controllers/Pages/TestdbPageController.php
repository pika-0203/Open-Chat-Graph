<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Recommend\RecommendUpdater;
use Shadow\DB;
use Shadow\Kernel\Validator;

class TestdbPageController
{
    function index(?string $token, ?string $page, ?string $limit)
    {
        if ($token !== 'inuinuINU1234' || !Validator::num($page, min: 1) || !Validator::num($limit, min: 1)) return false;

        /** @var RecommendUpdater $recommendUpdater */
        $recommendUpdater = app(RecommendUpdater::class);
        $tags = $recommendUpdater->getAllTagNames();

        return response($tags);
    }
}
