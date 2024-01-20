<?php

declare(strict_types=1);

namespace App\Views;

class SelectElementPagination
{
    /**
     * 昇順ページネーションのselect要素を生成
     * 
     * @return array `[$title, $_selectElement, $_label]`
     */
    function geneSelectElementPagerAsc(string $pagePath, string $queryString, int $pageNumber, int $totalRecords, int $itemsPerPage, int $maxPage, array $labelArray = []): array
    {
        // ページ番号の表示に必要な要素を取得する
        $getElement = function ($url, $selected, $start, $end, $i) use ($labelArray) {
            $startLabel = $labelArray[$start - 1] ?? '';
            $endLabel = $labelArray[$end - 1] ?? '';

            return "<option value='{$url}' {$selected}>{$endLabel} ~ {$startLabel} ({$end} ~ {$start})</option>";
        };

        // 選択されたページに対して"selected"属性を返す
        $selected = fn ($i) => ($i === $pageNumber) ? "selected='selected'" : '';

        // ページ番号に応じて、そのページの最初のインデックスを計算する
        $startNum = fn ($i) => ($i === 1) ? $totalRecords : $totalRecords - (($i - 1) * $itemsPerPage);

        // ページ番号に応じて、そのページの最後のインデックスを計算する
        $endNum = fn ($i) => ($i === $maxPage) ? 1 : $totalRecords - ($i * $itemsPerPage) + 1;

        // 各ページ番号の要素を生成する
        $_selectElement = '';
        for ($i = 1; $i <= $maxPage; $i++) {
            $_selectElement .= $getElement(pagerUrl($pagePath, $i) . $queryString, $selected($i), $startNum($i), $endNum($i), $i) . "\n";
        }

        // ラベルの番号を取得する
        $labelStartNum = $startNum($pageNumber);
        $labelEndNum = $endNum($pageNumber);

        $labelStart = $labelArray[$labelStartNum - 1] ?? '';
        $labelEnd = $labelArray[$labelEndNum - 1] ?? '';

        // select要素のラベルを生成する
        $_label = "{$labelEnd} ~ {$labelStart}<br>({$labelEndNum} ~ {$labelStartNum})";

        // タイトル用の文字列
        $title = "{$labelEndNum} ~ {$labelStartNum}";

        return [$title, $_selectElement, $_label];
    }
}
