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
    function getList(bool $shuffle = true): array
    {
        if (!$shuffle) return array_merge($this->hour, $this->day, $this->week, $this->member);

        $ranking5 = array_merge($this->day, $this->week);
        shuffle($ranking5);
        shuffle($this->hour);
        shuffle($this->member);
        return array_merge($this->hour, $ranking5, $this->member);
    }

    /** @return array{ id:int,name:string,img_url:string,member:int,table_name:string,emblem:int }[] */
    function getPreviewList(int $len): array
    {
        $ranking5 = array_merge($this->day, $this->week, $this->hour);
        shuffle($ranking5);

        $member = $this->member;
        shuffle($member);

        $result = array_merge($ranking5, $member);
        if(count($result) <= $len) return $result;

        $result = array_filter($result, fn ($el) => !in_array($el['img_url'], AppConfig::DEFAULT_OPENCHAT_IMG_URL_HASH));
        return array_slice($result, 0, $len);
    }

    function getCount(): int
    {
        return count($this->hour) + count($this->day) + count($this->week) + count($this->member);
    }
}
