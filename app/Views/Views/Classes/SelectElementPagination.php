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
    function geneSelectElementPagerAsc(int $pageNumber, int $totalRecords, int $itemsPerPage, int $maxPage): array
    {
        // ページ番号の表示に必要な要素を取得する
        $getElement = fn ($url, $selected, $start, $end, $i) => "<option value='{$url}' {$selected}>{$i}ページ ({$start} - {$end})</option>";

        // 選択されたページに対して"selected"属性を返す
        $selected = fn ($i) => ($i === $pageNumber) ? "selected='selected'" : '';

        // ページ番号に応じて、そのページの最初のインデックスを計算する
        $startNum = fn ($i) => ($i === 1) ? 1 : ($i - 1) * $itemsPerPage + 1;

        // ページ番号に応じて、そのページの最後のインデックスを計算する
        $endNum = fn ($i) => ($i === $totalRecords) ? $totalRecords : $i * $itemsPerPage;

        // 各ページ番号の要素を生成する
        $_selectElement = '';
        for ($i = 1; $i <= $maxPage; $i++) {
            $_selectElement .= $getElement(pagerUrl('ranking', $i), $selected($i), $startNum($i), $endNum($i), $i) . "\n";
        }

        // ラベルの番号を取得する
        $labelStartNum = $startNum($pageNumber);
        $labelEndNum = $endNum($pageNumber);

        // select要素のラベルを生成する
        $_label = "{$pageNumber}ページ ({$labelStartNum} - {$labelEndNum})";

        // タイトル用の文字列
        $title = "{$labelStartNum}〜{$labelEndNum}位";

        return [$title, $_selectElement, $_label];
    }
}
