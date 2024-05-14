<?php

declare(strict_types=1);

namespace App\Services\Recommend\Dto;

use App\Config\AppConfig;
use App\Services\Recommend\Enum\RecommendListType;

class RecommendListDto
{
    public int $maxMemberCount;

    /** @var array{ id:int,name:string,img_url:string,member:int,table_name:string,emblem:int } $list */
    function __construct(
        public RecommendListType $type,
        public string $listName,
        public array $hour,
        public array $day,
        public array $week,
        public array $member,
    ) {
        $elements = array_column(array_merge($hour, $day, $week, $member), 'member');
        $this->maxMemberCount = $elements ? max($elements) : 0;
    }

    /** @return array{ id:int,name:string,img_url:string,member:int,table_name:string,emblem:int }[] */
    function getList(bool $shuffle = true, ?int $limit = AppConfig::RECOMMEND_LIST_LIMIT): array
    {
        if (!$shuffle && $limit) {
            return array_slice(
                array_merge($this->hour, $this->day, $this->week, $this->member),
                0,
                $limit
            );
        } elseif (!$shuffle && !$limit) {
            return array_merge($this->hour, $this->day, $this->week, $this->member);
        }

        $hour = $this->hour;
        shuffle($hour);
        $length = $this->getSliceLength(count($hour));
        if (!$length) {
            return $hour;
        }

        $day = array_slice($this->day, 0, $length);
        shuffle($day);
        $length = $this->getSliceLength(count($hour) + count($day));
        if (!$length) {
            return array_merge($hour, $day);
        };

        $week = array_slice($this->week, 0, $length);
        shuffle($week);
        $length = $this->getSliceLength(count($hour) + count($day) + count($week));
        if (!$length) {
            $result = array_merge($day, $week);
            shuffle($result);
            return array_merge($hour, $result);
        };

        $member = array_slice($this->member, 0, $length);
        shuffle($member);

        $result = array_merge($day, $week);
        shuffle($result);
        return array_merge($hour, $result, $member);
    }

    private function getSliceLength(int $count)
    {
        $count = AppConfig::RECOMMEND_LIST_LIMIT - $count;
        return max($count, 0);
    }

    /** @return array{ id:int,name:string,img_url:string,member:int,table_name:string,emblem:int }[] */
    function getPreviewList(int $len): array
    {
        $ranking5 = array_merge($this->hour, $this->day, $this->week, $this->member);
        if (count($ranking5) <= $len) return $ranking5;
        return array_slice($ranking5, 0, $len);
    }

    function getCount(?int $limit = AppConfig::RECOMMEND_LIST_LIMIT): int
    {
        $totalCount = count($this->hour) + count($this->day) + count($this->week) + count($this->member);

        if (!$limit) {
            return $totalCount;
        } else {
            return $totalCount > $limit ? $limit : $totalCount;
        }
    }
}
