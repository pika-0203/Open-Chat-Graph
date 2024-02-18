<?php

declare(strict_types=1);

namespace App\Services\StaticData;

use App\Services\StaticData\Dto\StaticTopPageDto;
use App\Views\Dto\RankingArgDto;

class StaticDataFile
{
    function getTopPageData(): StaticTopPageDto
    {
        $data = getUnserializedFile('static_data_top/ranking_list.dat');
        if (!$data) {
            /** @var StaticDataGenerator $staticDataGenerator */
            $staticDataGenerator = app(StaticDataGenerator::class);
            return $staticDataGenerator->getTopPageDataFromDB();
        }

        return $data;
    }

    function getRankingArgDto(): RankingArgDto
    {
        /** @var StaticTopPageDto $data */
        $data = getUnserializedFile('static_data_top/ranking_arg_dto.dat');
        if (!$data) {
            /** @var StaticDataGenerator $staticDataGenerator */
            $staticDataGenerator = app(StaticDataGenerator::class);
            $data = $staticDataGenerator->getRankingArgDto();
        }

        $data->baseUrl = url();

        return $data;
    }
}
