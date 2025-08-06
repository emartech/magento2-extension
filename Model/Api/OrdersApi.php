<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\OrdersApiResponseInterface;
use Emartech\Emarsys\Api\Data\OrdersApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\OrdersApiInterface;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class OrdersApi implements OrdersApiInterface
{
    /**
     * @var OrderCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var OrderCollection
     */
    private $orderCollection;

    /**
     * @var OrdersApiResponseInterfaceFactory
     */
    private $responseFactory;

    /**
     * OrdersApi constructor.
     *
     * @param OrderCollectionFactory            $orderCollectionFactory
     * @param OrdersApiResponseInterfaceFactory $responseFactory
     */
    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        OrdersApiResponseInterfaceFactory $responseFactory
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
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
     * @return OrdersApiResponseInterface
     * @throws WebApiException
     */
    public function get(
        int $page,
        int $pageSize,
        int $sinceId = 0,
        ?string $storeId = null,
        ?string $lastUpdatedFrom = null,
        ?string $lastUpdatedTo = null
    ): OrdersApiResponseInterface {

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
            ->setCurrentPage($this->orderCollection->getCurPage())
            ->setLastPage($this->orderCollection->getLastPageNumber())
            ->setPageSize($this->orderCollection->getPageSize())
            ->setTotalCount($this->orderCollection->getSize())
            ->setItems($this->handleItems());
    }

    /**
     * InitCollection
     *
     * @return OrdersApi
     */
    private function initCollection(): OrdersApi
    {
        $this->orderCollection = $this->orderCollectionFactory->create();

        return $this;
    }

    /**
     * FilterStore
     *
     * @param string|null $storeId
     *
     * @return OrdersApi
     */
    private function filterStore(?string $storeId = null): OrdersApi
    {
        if ($storeId !== null) {
            if (!is_array($storeId)) {
                $storeId = explode(',', $storeId);
            }
            $this->orderCollection->addFieldToFilter('store_id', ['in' => $storeId]);
        }

        return $this;
    }

    /**
     * FilterSinceId
     *
     * @param int $sinceId
     *
     * @return OrdersApi
     */
    private function filterSinceId(int $sinceId = 0): OrdersApi
    {
        if ($sinceId) {
            $this->orderCollection
                ->addFieldToFilter('entity_id', ['gt' => $sinceId]);
        }

        return $this;
    }

    /**
     * Filter last updated at
     *
     * @param string|null $lastUpdatedFrom
     * @param string|null $lastUpdatedTo
     *
     * @return OrdersApi
     */
    private function filterLastUpdated(?string $lastUpdatedFrom = null, ?string $lastUpdatedTo = null): OrdersApi
    {
        if ($lastUpdatedFrom) {
            $this->orderCollection->addFieldToFilter('updated_at', ['gteq' => $lastUpdatedFrom]);
        }

        if ($lastUpdatedTo) {
            $this->orderCollection->addFieldToFilter('updated_at', ['lteq' => $lastUpdatedTo]);
        }

        return $this;
    }

    /**
     * SetPage
     *
     * @param int $page
     * @param int $pageSize
     *
     * @return OrdersApi
     */
    private function setPage(int $page, int $pageSize): OrdersApi
    {
        $this->orderCollection->setPage($page, $pageSize);

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

        /** @var Order $order */
        foreach ($this->orderCollection as $order) {
            $order->setStoreName(str_replace(PHP_EOL, ' ', $order->getStoreName()));
            $returnArray[] = $order;
        }

        return $returnArray;
    }
}
