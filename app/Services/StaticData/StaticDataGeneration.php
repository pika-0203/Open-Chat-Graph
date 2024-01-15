<?php

declare(strict_types=1);

namespace App\Services\StaticData;

class StaticDataGeneration
{
    /**
     * @return array `['openChatList' => array, 'pastWeekOpenChatList' => array, 'updatedAt' => int, 'recordCount' => int]`
     */
    function getTopPageData(): array
    {
        $data = getUnserializedArrayFromFile('static_data_top/ranking_list.dat');

        if (!$data) {
            /**
             * @var StaticTopPageDataGenerator $staticTopPageDataGenerator
             */
            $staticTopPageDataGenerator = app(StaticTopPageDataGenerator::class);
            $data = $staticTopPageDataGenerator->getTopPageDataFromDB();
        }

        return $data;
    }
}
