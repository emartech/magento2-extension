<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface ListApiResponseBaseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface ListApiResponseBaseInterface
{
    const PAGE_KEY        = 'page';
    const LAST_PAGE_KEY   = 'last_page';
    const PAGE_SIZE_KEY   = 'page_size';
    const TOTAL_COUNT_KEY = 'total_count';

    /**
     * @return int
     */
    public function getCurrentPage(): int;

    /**
     * @param int $currentPage
     *
     * @return $this
     */
    public function setCurrentPage($currentPage): ListApiResponseBaseInterface;

    /**
     * @return int
     */
    public function getLastPage(): int;

    /**
     * @param int $lastPage
     *
     * @return $this
     */
    public function setLastPage($lastPage): ListApiResponseBaseInterface;

    /**
     * @return int
     */
    public function getPageSize(): int;

    /**
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize): ListApiResponseBaseInterface;

    /**
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount($totalCount): ListApiResponseBaseInterface;
}
