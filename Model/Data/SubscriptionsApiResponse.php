<?php

namespace Emartech\Emarsys\Model\Data;

/**
 * Class SubscriptionsApiResponse
 * @package Emartech\Emarsys\Model\Data
 */
class SubscriptionsApiResponse extends \Magento\Framework\DataObject implements \Emartech\Emarsys\Api\Data\SubscriptionsApiResponseInterface
{
    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->getData(self::PAGE_KEY);
    }

    /**
     * @return \Emartech\Emarsys\Api\Data\SubscriptionInterface[]
     */
    public function getSubscriptions()
    {
        return $this->getData(self::SUBSCRIPTIONS_KEY);
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
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getData(self::TOTAL_COUNT_KEY);
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
     * @param \Emartech\Emarsys\Api\Data\SubscriptionInterface[] $subscriptions
     *
     * @return $this
     */
    public function setSubscriptions(array $subscriptions)
    {
        $this->setData(self::SUBSCRIPTIONS_KEY, $subscriptions);
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

    /**
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        $this->setData(self::TOTAL_COUNT_KEY, $totalCount);
        return $this;
    }
}
