<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface CustomersApiResponseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface CustomersApiResponseInterface
{
    const PAGE_KEY      = 'page';
    const CUSTOMERS_KEY = 'customers';
    const LAST_PAGE_KEY = 'last_page';
    const PAGE_SIZE_KEY = 'page_size';

    /**
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface[]
     */
    public function getCustomers();

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerInterface[] $customers
     *
     * @return $this
     */
    public function setCustomers(array $customers);

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
}
