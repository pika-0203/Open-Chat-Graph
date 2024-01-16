<?php

declare(strict_types=1);

namespace App\Views;

use App\Config\AppConfig;
use App\Views\SelectElementPagination;

//class RankingView
{
    private SelectElementPagination $pagination;

    function __construct(SelectElementPagination $pagination)
    {
        $this->pagination = $pagination;
    }

    public function finalizeRankingViewDto(RankingViewDto $dto): RankingViewDto
    {
        $dto->setProps(
            unserialize(file_get_contents(AppConfig::TOP_RANKING_INFO_FILE_PATH))
        );

        // メタタグ、構造化データ
        $subTitle = ($dto->pageNumber === 1) ? '' : "({$dto->pageNumber}ページ目)";
        $dto->_meta = meta()
            ->setTitle('【毎日更新】参加人数のランキング' . $subTitle)
            ->generateTags();

        $dto->_css = ['site_header', 'site_footer', 'room_list'];

        // ページネーションのselect要素
        [$title, $dto->_select, $dto->_label] = $this->pagination->geneSelectElementPagerAsc(
            $dto->pagePath,
            '',
            $dto->pageNumber,
            $dto->totalRecords,
            AppConfig::OPEN_CHAT_LIST_LIMIT,
            $dto->maxPageNumber
        );

        return $dto;
    }
}
