<?php

declare(strict_types=1);

namespace App\Views;

//class RankingViewDto
{
    // OpenChatStatisticsRanking
    public int $pageNumber;
    public int $maxPageNumber;
    public array $openChatList;
    public int $totalRecords;

    // RankingPageController
    public string $disabledBtnName;
    public string $pagePath;
    public bool $noindex;
    public string $_schema = '';

    // TOP_RANKING_INFO_FILE
    public int $rankingUpdatedAt;
    public int $rankingRowCount;
    public int $pastWeekRowCount;
    public int $recordCount;

    // RankingView
    public string $_meta;
    public array $_css;
    public string $_select;
    public string $_label;

    public function setProps(array|false $elements): static|false
    {
        if(!$elements) {
            return false;
        }

        foreach ($elements as $key => $element) {
            $this->$key = $element;
        }

        return $this;
    }

    public function isDisabledBtn(string $name): string
    {
        return ($name === $this->disabledBtnName) ? 'disabled' : '';
    }
}
