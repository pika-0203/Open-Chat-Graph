<?php

declare(strict_types=1);

namespace App\Services\StaticData;

use App\Services\StaticData\Dto\StaticRecommendPageDto;
use App\Services\StaticData\Dto\StaticTopPageDto;
use App\Views\Dto\RankingArgDto;

class StaticDataFile
{
    private function checkUpdatedAt(string $hourlyUpdatedAt)
    {
        if (!$hourlyUpdatedAt === getHouryUpdateTime())
            noStore();
    }

    function getTopPageData(): StaticTopPageDto
    {
        $data = getUnserializedFile('static_data_top/ranking_list.dat');

        /** @var StaticTopPageDto $data */
        if (!$data) {
            /** @var StaticDataGenerator $staticDataGenerator */
            $staticDataGenerator = app(StaticDataGenerator::class);
            return $staticDataGenerator->getTopPageDataFromDB();
        }

        $this->checkUpdatedAt($data->hourlyUpdatedAt->format('Y-m-d H:i:s'));
        return $data;
    }

    function getRankingArgDto(): RankingArgDto
    {
        /** @var RankingArgDto $data */
        $data = getUnserializedFile('static_data_top/ranking_arg_dto.dat');
        //$data = null;
        if (!$data) {
            /** @var StaticDataGenerator $staticDataGenerator */
            $staticDataGenerator = app(StaticDataGenerator::class);
            $data = $staticDataGenerator->getRankingArgDto();
        }

        $data->baseUrl = url();

        $this->checkUpdatedAt($data->hourlyUpdatedAt);
        return $data;
    }

    function getRecommendPageDto(): StaticRecommendPageDto
    {
        /** @var StaticRecommendPageDto $data */
        $data = getUnserializedFile('static_data_top/recommend_page_dto.dat');
        //$data = null;
        if (!$data) {
            /** @var StaticDataGenerator $staticDataGenerator */
            $staticDataGenerator = app(StaticDataGenerator::class);
            $data = $staticDataGenerator->getRecommendPageDto();
        }

        $this->checkUpdatedAt($data->hourlyUpdatedAt);
        return $data;
    }

    /** @return array<int, array<array{tag:string, record_count:int}>> */
    function getTagList(): array
    {
        /** @var array $data */
        $data = getUnserializedFile('static_data_top/tag_list.dat');

        if (!$data) {
            /** @var StaticDataGenerator $staticDataGenerator */
            $staticDataGenerator = app(StaticDataGenerator::class);
            $data = $staticDataGenerator->getTagList();
        }

        $time = getStorageFileTime('static_data_top/tag_list.dat');
        if (!$time || new \DateTime('@' . $time) < new \DateTime(getHouryUpdateTime()))
            noStore();

        return $data;
    }
}
