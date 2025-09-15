<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\RefundsApiResponseInterface;
use Emartech\Emarsys\Api\Data\RefundsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\RefundsApiInterface;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection as CreditmemoCollection;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollectionFactory;

class RefundsApi implements RefundsApiInterface
{
    /**
     * @var CreditmemoCollectionFactory
     */
    private $creditmemoCollectionFactory;

    /**
     * @var CreditmemoCollection
     */
    private $creditmemoCollection;

    /**
     * @var RefundsApiResponseInterfaceFactory
     */
    private $responseFactory;

    /**
     * OrdersApi constructor.
     *
     * @param CreditmemoCollectionFactory        $creditmemoCollectionFactory
     * @param RefundsApiResponseInterfaceFactory $responseFactory
     */
    public function __construct(
        CreditmemoCollectionFactory $creditmemoCollectionFactory,
        RefundsApiResponseInterfaceFactory $responseFactory
    ) {
        $this->creditmemoCollectionFactory = $creditmemoCollectionFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Get
     *
     * @param int         $page
     * @param int         $pageSize
     * @param int         $sinceId
     * @param string|null $storeId
     * @param string|null $lastUpdatedFrom
     * @param string|null $lastUpdatedTo
     *
     * @return RefundsApiResponseInterface
     * @throws WebApiException
     */
    public function get(
        int $page,
        int $pageSize,
        int $sinceId = 0,
        ?string $storeId = null,
        ?string $lastUpdatedFrom = null,
        ?string $lastUpdatedTo = null
    ): RefundsApiResponseInterface {

        if (empty($storeId)) {
            throw new WebApiException(__('Store ID is required'));
        }

        $this
            ->initCollection()
            ->filterStore($storeId)
            ->filterSinceId($sinceId)
            ->filterLastUpdated($lastUpdatedFrom, $lastUpdatedTo)
            ->setPage($page, $pageSize);

        return $this->responseFactory
            ->create()
            ->setCurrentPage($this->creditmemoCollection->getCurPage())
            ->setLastPage($this->creditmemoCollection->getLastPageNumber())
            ->setPageSize($this->creditmemoCollection->getPageSize())
            ->setTotalCount($this->creditmemoCollection->getSize())
            ->setItems($this->handleItems());
    }

    /**
     * InitCollection
     *
     * @return RefundsApi
     */
    private function initCollection(): RefundsApi
    {
        $this->creditmemoCollection = $this->creditmemoCollectionFactory->create();

        return $this;
    }

    /**
     * FilterStore
     *
     * @param string|null $storeId
     *
     * @return RefundsApi
     */
    private function filterStore(?string $storeId = null): RefundsApi
    {
        if ($storeId !== null) {
            if (!is_array($storeId)) {
                $storeId = explode(',', $storeId);
            }
            $this->creditmemoCollection->addFieldToFilter('store_id', ['in' => $storeId]);
        }

        return $this;
    }

    /**
     * FilterSinceId
     *
     * @param int $sinceId
     *
     * @return RefundsApi
     */
    private function filterSinceId(int $sinceId = 0): RefundsApi
    {
        if ($sinceId) {
            $this->creditmemoCollection->addFieldToFilter('entity_id', ['gt' => $sinceId]);
        }

        return $this;
    }

    /**
     * Filter last updated at
     *
     * @param string|null $lastUpdatedFrom
     * @param string|null $lastUpdatedTo
     *
     * @return RefundsApi
     */
    private function filterLastUpdated(?string $lastUpdatedFrom = null, ?string $lastUpdatedTo = null): RefundsApi
    {
        if ($lastUpdatedFrom) {
            $this->creditmemoCollection->addFieldToFilter('updated_at', ['gteq' => $lastUpdatedFrom]);
        }

        if ($lastUpdatedTo) {
            $this->creditmemoCollection->addFieldToFilter('updated_at', ['lteq' => $lastUpdatedTo]);
        }

        return $this;
    }

    /**
     * SetPage
     *
     * @param int $page
     * @param int $pageSize
     *
     * @return RefundsApi
     */
    private function setPage(int $page, int $pageSize): RefundsApi
    {
        $this->creditmemoCollection->setPage($page, $pageSize);

        return $this;
    }

    /**
     * HandleItems
     *
     * @return array
     */
    private function handleItems(): array
    {
        $returnArray = [];

        foreach ($this->creditmemoCollection as $creditmemo) {
            $returnArray[] = $creditmemo;
        }

        return $returnArray;
    }
}
