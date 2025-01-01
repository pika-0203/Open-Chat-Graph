<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Config\AppConfig;
use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Updater\OpenChatImageStoreUpdater;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class OpenChatImageUpdater
{
    function __construct(
        private UpdateOpenChatRepositoryInterface $updateOpenChatRepository,
        private OpenChatImageStoreUpdater $openChatImageStoreUpdater,
    ) {}

    function hourlyImageUpdate()
    {
        $updated = $this->updateOpenChatRepository->getUpdatedOpenChatBetweenUpdatedAt(
            OpenChatServicesUtility::getModifiedCronTime('now'),
            OpenChatServicesUtility::getModifiedCronTime(strtotime('+1hour'))
        );

        $this->update($updated);
    }

    function imageUpdateAll(bool $forToday = true)
    {
        if (!$forToday)  $this->update($this->updateOpenChatRepository->getOpenChatImgAll());

        $date = new \DateTime(OpenChatServicesUtility::getCronModifiedStatsMemberDate());
        $date->modify('- 1day');
        $this->update($this->updateOpenChatRepository->getOpenChatImgAll($date->format('Y-m-d')));
    }

    private function update(array $ocArray)
    {
        // 開発環境の場合、更新制限をかける
        if (AppConfig::$isDevlopment ?? false) {
            $limit = AppConfig::$developmentEnvUpdateLimit['OpenChatImageUpdater'] ?? 1;
            $ocArrayCount = count($ocArray);
            $ocArray = array_slice($ocArray, 0, $limit);
            addCronLog("Development environment. Update limit: {$limit} / {$ocArrayCount}");
        }

        foreach ($ocArray as $oc) {
            if (base62Hash($oc['img_url']) === $oc['local_img_url']) {
                continue;
            }

            $this->openChatImageStoreUpdater->updateImage($oc['id'], $oc['img_url'], $oc['local_img_url']);
        }
    }
}
