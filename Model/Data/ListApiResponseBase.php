<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\ListApiResponseBaseInterface;

/**
 * Class ListApiResponseBase
 * @package Emartech\Emarsys\Model\Data
 */
class ListApiResponseBase extends DataObject implements ListApiResponseBaseInterface
{
    /**
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->getData(self::PAGE_KEY);
    }

    /**
     * @return int
     */
    public function getLastPage(): int
    {
        return $this->getData(self::LAST_PAGE_KEY);
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->getData(self::PAGE_SIZE_KEY);
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->getData(self::TOTAL_COUNT_KEY);
    }

    /**
     * @param int $currentPage
     *
     * @return $this
     */
    public function setCurrentPage($currentPage): ListApiResponseBaseInterface
    {
        $this->setData(self::PAGE_KEY, $currentPage);
        return $this;
    }

    /**
     * @param int $lastPage
     *
     * @return $this
     */
    public function setLastPage($lastPage): ListApiResponseBaseInterface
    {
        $this->setData(self::LAST_PAGE_KEY, $lastPage);
        return $this;
    }

    /**
     * @param int $pageSize
     *
     * @return $this
     */
    public function setPageSize($pageSize): ListApiResponseBaseInterface
    {
        $this->setData(self::PAGE_SIZE_KEY, $pageSize);
        return $this;
    }

    /**
     * @param int $totalCount
     *
     * @return $this
     */
    public function setTotalCount($totalCount): ListApiResponseBaseInterface
    {
        $this->setData(self::TOTAL_COUNT_KEY, $totalCount);
        return $this;
    }
}
