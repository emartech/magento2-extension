<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ListApiResponseBaseInterface;
use Magento\Framework\DataObject;

class ListApiResponseBase extends DataObject implements ListApiResponseBaseInterface
{
    /**
     * GetCurrentPage
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return (int) $this->getData(self::PAGE_KEY);
    }

    /**
     * GetLastPage
     *
     * @return int
     */
    public function getLastPage(): int
    {
        return (int) $this->getData(self::LAST_PAGE_KEY);
    }

    /**
     * GetPageSize
     *
     * @return int
     */
    public function getPageSize(): int
    {
        return (int) $this->getData(self::PAGE_SIZE_KEY);
    }

    /**
     * GetTotalCount
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return (int) $this->getData(self::TOTAL_COUNT_KEY);
    }

    /**
     * SetCurrentPage
     *
     * @param int $currentPage
     *
     * @return ListApiResponseBaseInterface
     */
    public function setCurrentPage(int $currentPage): ListApiResponseBaseInterface
    {
        $this->setData(self::PAGE_KEY, $currentPage);

        return $this;
    }

    /**
     * SetLastPage
     *
     * @param int $lastPage
     *
     * @return ListApiResponseBaseInterface
     */
    public function setLastPage(int $lastPage): ListApiResponseBaseInterface
    {
        $this->setData(self::LAST_PAGE_KEY, $lastPage);

        return $this;
    }

    /**
     * SetPageSize
     *
     * @param int $pageSize
     *
     * @return ListApiResponseBaseInterface
     */
    public function setPageSize(int $pageSize): ListApiResponseBaseInterface
    {
        $this->setData(self::PAGE_SIZE_KEY, $pageSize);

        return $this;
    }

    /**
     * SetTotalCount
     *
     * @param int $totalCount
     *
     * @return ListApiResponseBaseInterface
     */
    public function setTotalCount(int $totalCount): ListApiResponseBaseInterface
    {
        $this->setData(self::TOTAL_COUNT_KEY, $totalCount);

        return $this;
    }
}
