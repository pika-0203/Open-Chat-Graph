<?php

namespace App\Views\Traits;

trait TraitPaginationIndexCalculator
{
    /**
     * Calculates the record index for the current page in descending order based on the given parameters.
     * If $maxPage is specified, it returns the ending index for the current page in descending order.
     * 
     * @param int $pageNumber    The current page number.
     * @param int $totalRecords  The total number of records.
     * @param int $itemsPerPage  The number of items to display per page.
     * @param int|null $maxPage  [optional] The total number of pages needed to display all records in descending order.
     * @return int               The record index for the current page in descending order.
     */
    function calcDescRecordIndex(int $pageNumber, int $totalRecords, int $itemsPerPage, ?int $maxPage = null): int
    {
        if ($maxPage === null) {
            if ($pageNumber === 1) {
                return $totalRecords;
            }

            return $totalRecords - $itemsPerPage * ($pageNumber - 1);
        }

        if ($pageNumber === $maxPage) {
            return 1;
        }

        return $totalRecords - $itemsPerPage * $pageNumber;
    }
}
