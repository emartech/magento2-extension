<?php

namespace Emartech\Emarsys\Model\Api;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;


use Emartech\Emarsys\Api\OrdersApiInterface;
use Emartech\Emarsys\Api\Data\OrdersApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\OrdersApiResponseInterface;

/**
 * Class SystemApi
 * @package Emartech\Emarsys\Model\Api
 */
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
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $storeId
     *
     * @return OrdersApiResponseInterface
     */
    public function get($page, $pageSize, $storeId = null)
    {
        $this
            ->initCollection()
            ->filterStore($storeId)
            ->setPage($page, $pageSize);

        return $this->responseFactory->create()
            ->setCurrentPage($this->orderCollection->getCurPage())
            ->setLastPage($this->orderCollection->getLastPageNumber())
            ->setPageSize($this->orderCollection->getPageSize())
            ->setTotalCount($this->orderCollection->getSize())
            ->setItems($this->handleItems());
    }

    /**
     * @return $this
     */
    private function initCollection()
    {
        $this->orderCollection = $this->orderCollectionFactory->create();

        return $this;
    }

    /**
     * @param int|string|null $storeId
     *
     * @return $this
     */
    private function filterStore($storeId = null)
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
     * @param $page
     * @param $pageSize
     *
     * @return $this
     */
    private function setPage($page, $pageSize)
    {
        $this->orderCollection->setPage($page, $pageSize);

        return $this;
    }

    /**
     * @return array
     */
    private function handleItems()
    {
        $returnArray = [];

        foreach ($this->orderCollection as $order) {
            $returnArray[] = $order;
        }

        return $returnArray;
    }
}
