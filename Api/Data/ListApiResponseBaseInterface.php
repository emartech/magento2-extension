<?php

namespace Emartech\Emarsys\Api\Data;

interface ListApiResponseBaseInterface
{
    public const PAGE_KEY        = 'page';
    public const LAST_PAGE_KEY   = 'last_page';
    public const PAGE_SIZE_KEY   = 'page_size';
    public const TOTAL_COUNT_KEY = 'total_count';

    /**
     * GetCurrentPage
     *
     * @return int
     */
    public function getCurrentPage(): int;

    /**
     * SetCurrentPage
     *
     * @param int $currentPage
     *
     * @return \Emartech\Emarsys\Api\Data\ListApiResponseBaseInterface
     */
    public function setCurrentPage(int $currentPage): ListApiResponseBaseInterface;

    /**
     * GetLastPage
     *
     * @return int
     */
    public function getLastPage(): int;

    /**
     * SetLastPage
     *
     * @param int $lastPage
     *
     * @return \Emartech\Emarsys\Api\Data\ListApiResponseBaseInterface
     */
    public function setLastPage(int $lastPage): ListApiResponseBaseInterface;

    /**
     * GetPageSize
     *
     * @return int
     */
    public function getPageSize(): int;

    /**
     * SetPageSize
     *
     * @param int $pageSize
     *
     * @return \Emartech\Emarsys\Api\Data\ListApiResponseBaseInterface
     */
    public function setPageSize(int $pageSize): ListApiResponseBaseInterface;

    /**
     * GetTotalCount
     *
     * @return int
     */
    public function getTotalCount(): int;

    /**
     * SetTotalCount
     *
     * @param int $totalCount
     *
     * @return \Emartech\Emarsys\Api\Data\ListApiResponseBaseInterface
     */
    public function setTotalCount(int $totalCount): ListApiResponseBaseInterface;
}
