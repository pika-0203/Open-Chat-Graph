<?php

declare(strict_types=1);

namespace App\Services\Recommend\Dto;

use App\Services\Recommend\Enum\RecommendListType;

class RecommendListDto
{
    public int $maxMemberCount;

    /** @var array{ id:int,name:string,img_url:string,member:int,table_name:string } $list */
    function __construct(
        public RecommendListType $type,
        public string $listName,
        public array $hour,
        public array $day,
        public array $week,
        public array $member,
    ) {
        $this->maxMemberCount = max(
            array_column(array_merge($hour, $day, $week, $member), 'member')
        );
    }

    function getList(bool $shuffle = true)
    {
        if (!$shuffle) return array_merge($this->hour, $this->day, $this->week, $this->member);

        $ranking5 = array_merge($this->day, $this->week);
        shuffle($ranking5);
        shuffle($this->hour);
        shuffle($this->member);
        return array_merge($this->hour, $ranking5, $this->member);
    }
}
