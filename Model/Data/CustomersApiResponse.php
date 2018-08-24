<?php

namespace Emartech\Emarsys\Model\Data;

/**
 * Class CustomersApiResponse
 * @package Emartech\Emarsys\Model\Data
 */
class CustomersApiResponse extends \Magento\Framework\DataObject implements \Emartech\Emarsys\Api\Data\CustomersApiResponseInterface
{
    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData(self::PAGE_KEY);
    }

    /**
     * @return \Emartech\Emarsys\Api\Data\CustomerInterface[]
     */
    public function getCustomers()
    {
        return $this->getData(self::CUSTOMERS_KEY);
    }

    /**
     * @return int
     */
    public function getLastPage()
    {
        return $this->getData(self::LAST_PAGE_KEY);
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->getData(self::PAGE_SIZE_KEY);
    }

    /**
     * @param int $currentPage
     *
     * @return $this
     */
    public function setCurrentPage($currentPage)
    {
        $this->setData(self::PAGE_KEY, $currentPage);
        return $this;
    }

    /**
     * @param \Emartech\Emarsys\Api\Data\CustomerInterface[] $customers
     *
     * @return $this
     */
    public function setCustomers(array $customers)
    {
        $this->setData(self::CUSTOMERS_KEY, $customers);
        return $this;
    }

    /**
     * @param int $lastPage
     *
     * @return $this
     */
    public function setLastPage($lastPage)
    {
        $this->setData(self::LAST_PAGE_KEY, $lastPage);
        return $this;
    }

    /**
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        $this->setData(self::PAGE_SIZE_KEY, $pageSize);
        return $this;
    }
}
