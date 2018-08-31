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
    public function getCurrentPage();

    /**
     * @param int $currentPage
     *
     * @return $this
     */
    public function setCurrentPage($currentPage);

    /**
     * @return int
     */
    public function getLastPage();

    /**
     * @param int $lastPage
     *
     * @return $this
     */
    public function setLastPage($lastPage);

    /**
     * @return int
     */
    public function getPageSize();

    /**
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize);

    /**
     * @return int
     */
    public function getTotalCount();

    /**
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount($totalCount);
}
