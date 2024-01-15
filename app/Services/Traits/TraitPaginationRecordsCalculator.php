<?php

namespace App\Services\Traits;

trait TraitPaginationRecordsCalculator
{
    /**
     * Calculates the starting index of records for a given page number and number of items per page.
     *
     * @param int $pageNumber            The current page number.
     * @param int $numberOfItemsPerPage  The number of items to display per page.
     * @return int                       The calculated offset.
     */
    public function calcOffset(int $pageNumber, int $numberOfItemsPerPage): int
    {
        $offset = $pageNumber >= 1 ? $numberOfItemsPerPage * ($pageNumber - 1) : 0;
        return $offset;
    }

    /**
     * Calculates the maximum number of pages needed to display a set of records,
     * given the total number of records and the number of records to display per page.
     *
     * @param int $totalRecords  The total number of records to display.
     * @param int $recordsPerPage  The number of records to display per page.
     * @return int               The maximum number of pages needed to display all the records.
     */
    public function calcMaxPages(int $totalRecords, int $recordsPerPage): int
    {
        return (int) ceil($totalRecords / $recordsPerPage);
    }

    /**
     * Calculates the ending index of records for a given page number and number of items per page.
     *
     * @param int $pageNumber            The current page number.
     * @param int $numberOfItemsPerPage  The number of items to display per page.
     * @return int                       The calculated ending index of records.
     */
    public function calcEndId(int $pageNumber, int $numberOfItemsPerPage): int
    {
        return $pageNumber >= 1 ? ($numberOfItemsPerPage * $pageNumber) : $numberOfItemsPerPage;
    }
}