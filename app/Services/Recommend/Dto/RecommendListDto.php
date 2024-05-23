<?php

declare(strict_types=1);

namespace App\Services\Recommend\Dto;

use App\Config\AppConfig;
use App\Services\Recommend\Enum\RecommendListType;
use App\Services\Recommend\RecommendTagFilters;
use App\Services\Recommend\RecommendUtility;

class RecommendListDto
{
    public int $maxMemberCount;
    public array $mergedElements;
    public ?array $shuffledMergedElements = null;

    /** @var array{ id:int,name:string,img_url:string,member:int,table_name:string,emblem:int } $list */
    function __construct(
        public RecommendListType $type,
        public string $listName,
        public array $hour,
        public array $day,
        public array $week,
        public array $member,
        public string $hourlyUpdatedAt
    ) {
        $this->mergedElements = array_merge($hour, $day, $week, $member);

        $elements = array_column($this->mergedElements, 'member');
        $this->maxMemberCount = $elements ? max($elements) : 0;
    }

    private function getSliceLength(int $count)
    {
        $count = AppConfig::RECOMMEND_LIST_LIMIT - $count;
        return max($count, 0);
    }

    /** @return array{ id:int,name:string,img_url:string,member:int,table_name:string,emblem:int }[] */
    private function buildShuffledList(): array
    {
        if (is_array($this->shuffledMergedElements) && $this->shuffledMergedElements)
            return $this->shuffledMergedElements;

        $hour = $this->hour;
        shuffle($hour);
        $length = $this->getSliceLength(count($hour));
        if (!$length) {
            $this->shuffledMergedElements = $hour;
            return $this->shuffledMergedElements;
        }

        $day = array_slice($this->day, 0, $length);
        shuffle($day);
        $length = $this->getSliceLength(count($hour) + count($day));
        if (!$length) {
            $this->shuffledMergedElements = array_merge($hour, $day);
            return $this->shuffledMergedElements;
        };

        $week = array_slice($this->week, 0, $length);
        shuffle($week);
        $length = $this->getSliceLength(count($hour) + count($day) + count($week));
        if (!$length) {
            $result = array_merge($day, $week);
            shuffle($result);
            $this->shuffledMergedElements = array_merge($hour, $result);
            return $this->shuffledMergedElements;
        };

        $member = array_slice($this->member, 0, $length);
        shuffle($member);

        $result = array_merge($day, $week);
        shuffle($result);

        $this->shuffledMergedElements = array_merge($hour, $result, $member);
        return $this->shuffledMergedElements;
    }

    /** @return string[] */
    private function buildFilterdTags(array $mergedElements, bool $shuffle): array
    {
        $tag = $this->type === RecommendListType::Tag ? $this->listName : '';
        $tagName = $this->type === RecommendListType::Tag ? $this->listName : '';
        $tagStr = RecommendUtility::extractTag($tag);

        $tags = sortAndUniqueArray(
            array_merge(
                array_column($mergedElements, 'tag1'),
                array_column($mergedElements, 'tag2'),
                RecommendTagFilters::FilteredTagSort[$tag] ?? []
            ),
            1
        );

        $tags = array_filter(
            $tags,
            fn ($e) => (
                !in_array($e, RecommendTagFilters::RecommendPageTagFilter)
                || (
                    isset(RecommendTagFilters::FilteredTagSort[$tag])
                    && in_array($e, RecommendTagFilters::FilteredTagSort[$tag])
                )
            ) && $e !== $tagName
        );

        $tagsStr = array_map(fn ($t) => RecommendUtility::extractTag($t), $tags);

        uksort($tags, function ($a) use ($tagStr, $tagsStr, $tag, $tags) {
            return str_contains(
                $tagsStr[$a],
                $tagStr
            ) || (
                isset(RecommendTagFilters::FilteredTagSort[$tag])
                && in_array($tags[$a], RecommendTagFilters::FilteredTagSort[$tag])
            ) ? -1 : 1;
        });

        return $tags;
    }

    function getList(bool $shuffle = true, ?int $limit = AppConfig::TOP_RANKING_LIST_LIMIT): array
    {
        $elements = $shuffle ? $this->buildShuffledList() : $this->mergedElements;
        $result = $limit ? array_slice($elements, 0, $limit) : $elements;
        return $result;
    }

    /** @return array{ id:int,name:string,img_url:string,member:int,table_name:string,emblem:int }[] */
    function getPreviewList(int $len): array
    {
        return array_slice($this->mergedElements, 0, $len);
    }

    function getCount(): int
    {
        return count($this->mergedElements);
    }

    /** @return string[] */
    function getFilterdTags(bool $shuffle = true, ?int $limit = AppConfig::TOP_RANKING_LIST_LIMIT): array
    {
        return $this->buildFilterdTags($this->getList($shuffle, $limit), $shuffle);
    }
}
