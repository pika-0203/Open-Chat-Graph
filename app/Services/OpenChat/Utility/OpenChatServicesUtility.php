<?php

namespace App\Services\OpenChat\Utility;

use App\Config\AppConfig;

class OpenChatServicesUtility
{
    private static ?\DateTime $date = null;

    /**
     * @return bool 収集を拒否している場合は true
     */
    static function containsHashtagNolog(string $desc): bool
    {
        return strpos($desc, '#nolog') !== false;
    }

    /**
     * @return string Y-m-d
     */
    static function getCronModifiedStatsMemberDate(): string
    {
        if (self::$date) {
            return self::$date->format('Y-m-d');
        }
        
        self::$date = new \DateTime();
        self::$date->setTimeZone(new \DateTimeZone('Asia/Tokyo'));

        if ((int)self::$date->format('H') < AppConfig::CRON_MERGER_HOUR_RANGE_START) {
            self::$date->modify('-1 day');
        } else if ((int)self::$date->format('i') < AppConfig::CRON_START_MINUTE) {
            self::$date->modify('-1 day');
        }

        return self::$date->format('Y-m-d');
    }

    static function getModifiedCronTime(int $time): \DateTime
    {
        $fileTime = new \DateTime('@' . $time);
        $fileTime->setTimeZone(new \DateTimeZone('Asia/Tokyo'));

        if ((int)$fileTime->format('i') < AppConfig::CRON_START_MINUTE) {
            $fileTime->modify('-1 hour');
        }

        $fileTime->setTime((int)$fileTime->format('H'), AppConfig::CRON_START_MINUTE);

        return $fileTime;
    }
}
