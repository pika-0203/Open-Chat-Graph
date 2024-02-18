<?php

declare(strict_types=1);

namespace App\Services\StaticData;

use App\Config\AppConfig;
use App\Models\Repositories\OpenChatListRepositoryInterface;
use App\Services\StaticData\Dto\StaticTopPageDto;
use App\Views\Dto\RankingArgDto;

class StaticDataGenerator
{
    function __construct(
        private OpenChatListRepositoryInterface $openChatListRepository,
    ) {
    }

    function getTopPageDataFromDB(): StaticTopPageDto
    {
        // トップページのキャッシュファイルを生成する
        $dto = new StaticTopPageDto;
        $dto->hourlyList = $this->openChatListRepository->findMemberStatsHourlyRanking(0, 5);
        $dto->dailyList = $this->openChatListRepository->findMemberStatsDailyRanking(0, 5);
        $dto->weeklyList = $this->openChatListRepository->findMemberStatsPastWeekRanking(0, 5);
        $dto->popularList = $this->openChatListRepository->findMemberCountRanking(5);

        // 説明文の文字数を詰める
        trimOpenChatListDescriptions($dto->hourlyList);
        trimOpenChatListDescriptions($dto->dailyList);
        trimOpenChatListDescriptions($dto->weeklyList);
        trimOpenChatListDescriptions($dto->popularList);

        $dto->hourlyUpdatedAt = new \DateTime(file_get_contents(AppConfig::HOURLY_CRON_UPDATED_AT_DATETIME));
        $dto->dailyUpdatedAt = new \DateTime(file_get_contents(AppConfig::DAILY_CRON_UPDATED_AT_DATE));

        return $dto;
    }

    function getRankingArgDto(): RankingArgDto
    {
        $_argDto = new RankingArgDto;
        $_argDto->rankingUpdatedAt = convertDatetime(file_get_contents(AppConfig::HOURLY_REAL_UPDATED_AT_DATETIME), true);
        $_argDto->hourlyUpdatedAt = file_get_contents(AppConfig::HOURLY_CRON_UPDATED_AT_DATETIME);
        $_argDto->modifiedUpdatedAtDate = file_get_contents(AppConfig::DAILY_CRON_UPDATED_AT_DATE);;
        $_argDto->subCategories = json_decode(file_get_contents(AppConfig::OPEN_CHAT_SUB_CATEGORIES_FILE_PATH), true);

        return $_argDto;
    }

    function updateStaticData()
    {
        safeFileRewrite(AppConfig::HOURLY_REAL_UPDATED_AT_DATETIME, (new \DateTime)->format('Y-m-d H:i:s'));
        saveSerializedFile('static_data_top/ranking_list.dat', $this->getTopPageDataFromDB());
        saveSerializedFile('static_data_top/ranking_arg_dto.dat', $this->getRankingArgDto());
    }
}
