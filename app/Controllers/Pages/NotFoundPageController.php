<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\StaticData\StaticDataFile;
use Shadow\Kernel\ViewInterface;

class NotFoundPageController
{
    function __construct(
        private StaticDataFile $staticDataGeneration,
    ) {
    }

    function index(): ViewInterface
    {
        $dto = $this->staticDataGeneration->getTopPageData();

        $hourlyEnd = $dto->hourlyUpdatedAt->format('G:i');
        $dto->hourlyUpdatedAt->modify('-1hour');
        $hourlyStart = $dto->hourlyUpdatedAt->format('G:i');
        $hourlyRange = "{$hourlyStart} 〜 {$hourlyEnd}";

        return view('components/not_found_list', compact(
            'dto',
            'hourlyRange',
        ));
    }
}
