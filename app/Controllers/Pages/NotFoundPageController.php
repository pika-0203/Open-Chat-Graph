<?php

declare(strict_types=1);

namespace App\Controllers\Pages;

use App\Services\Recommend\Dto\RecommendListDto;
use App\Services\StaticData\StaticDataFile;
use Shadow\Kernel\ViewInterface;

class NotFoundPageController
{
    function __construct(
        private StaticDataFile $staticDataGeneration,
    ) {
    }

    /** @param null|array{0:RecommendListDto|false, 1:RecommendListDto|false, 2:string|false} $recommend */
    function index(?array $recommend = null): ViewInterface
    {
        $dto = $this->staticDataGeneration->getTopPageData();

        $hourlyEnd = $dto->hourlyUpdatedAt->format('G:i');
        $dto->hourlyUpdatedAt->modify('-1hour');
        $hourlyStart = $dto->hourlyUpdatedAt->format('G:i');
        $hourlyRange = "{$hourlyStart} 〜 {$hourlyEnd}";

        return view('components/not_found_list', compact(
            'dto',
            'hourlyRange',
            'recommend'
        ));
    }
}
