<?php

declare(strict_types=1);

namespace App\Services\OpenChat;

use App\Models\Repositories\UpdateOpenChatRepositoryInterface;
use App\Services\OpenChat\Updater\OpenChatImageStoreUpdater;
use App\Services\OpenChat\Utility\OpenChatServicesUtility;

class OpenChatImageUpdater
{
    function __construct(
        private UpdateOpenChatRepositoryInterface $updateOpenChatRepository,
        private OpenChatImageStoreUpdater $openChatImageStoreUpdater,
    ) {
    }

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
        $this->update($this->updateOpenChatRepository->getOpenChatImgAll(
            $forToday ? OpenChatServicesUtility::getCronModifiedStatsMemberDate() : null
        ));
    }

    private function update(array $ocArray)
    {
        foreach ($ocArray as $oc) {
            if (base62Hash($oc['img_url']) === $oc['local_img_url']) {
                continue;
            }

            $this->openChatImageStoreUpdater->updateImage($oc['id'], $oc['img_url'], $oc['local_img_url']);
        }
    }
}
