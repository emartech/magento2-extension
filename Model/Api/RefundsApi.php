<?php

namespace Emartech\Emarsys\Model\Api;

use Magento\Sales\Model\ResourceModel\Order\Creditmemo\CollectionFactory as CreditmemoCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection as CreditmemoCollection;
use Magento\Framework\Webapi\Exception as WebApiException;

use Emartech\Emarsys\Api\RefundsApiInterface;
use Emartech\Emarsys\Api\Data\RefundsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\RefundsApiResponseInterface;

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
     * @param int         $page
     * @param int         $pageSize
     * @param int         $sinceId
     * @param string|null $storeId
     *
     * @return RefundsApiResponseInterface
     * @throws WebApiException
     */
    public function get($page, $pageSize, $sinceId = 0, $storeId = null)
    {
        if (empty($storeId)) {
            throw new WebApiException(__('Store ID is required'));
        }

        $this
            ->initCollection()
            ->filterStore($storeId)
            ->filterSinceId($sinceId)
            ->setPage($page, $pageSize);

        return $this->responseFactory->create()
            ->setCurrentPage($this->creditmemoCollection->getCurPage())
            ->setLastPage($this->creditmemoCollection->getLastPageNumber())
            ->setPageSize($this->creditmemoCollection->getPageSize())
            ->setTotalCount($this->creditmemoCollection->getSize())
            ->setItems($this->handleItems());
    }

    /**
     * @return $this
     */
    private function initCollection()
    {
        $this->creditmemoCollection = $this->creditmemoCollectionFactory->create();

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
            $this->creditmemoCollection->addFieldToFilter('store_id', ['in' => $storeId]);
        }

        return $this;
    }

    /**
     * @param int $sinceId
     *
     * @return $this
     */
    private function filterSinceId($sinceId = 0)
    {
        if ($sinceId) {
            $this->creditmemoCollection->addFieldToFilter('entity_id', ['gt' => $sinceId]);
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
        $this->creditmemoCollection->setPage($page, $pageSize);

        return $this;
    }

    /**
     * @return array
     */
    private function handleItems()
    {
        $returnArray = [];

        foreach ($this->creditmemoCollection as $creditmemo) {
            $returnArray[] = $creditmemo;
        }

        return $returnArray;
    }
}
