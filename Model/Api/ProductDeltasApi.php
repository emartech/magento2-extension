<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\ProductDeltaRepositoryInterface;
use Emartech\Emarsys\Api\ProductDeltasApiInterface;
use Emartech\Emarsys\Model\ResourceModel\ProductDelta\Collection as ProductDeltaCollection;
use Emartech\Emarsys\Model\ResourceModel\ProductDelta\CollectionFactory as ProductDeltaCollectionFactory;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Webapi\Exception as WebApiException;

class ProductDeltasApi implements ProductDeltasApiInterface
{
    /**
     * @var ProductDeltaRepositoryInterface
     */
    private $productDeltaRepository;

    /**
     * @var ProductDeltaCollectionFactory
     */
    private $productDeltaCollectionFactory;

    /**
     * @var ProductDeltaCollection|null
     */
    private $productDeltaCollection;

    /**
     * ProductDeltasApi constructor.
     *
     * @param ProductDeltaRepositoryInterface $productDeltaRepository
     * @param ProductDeltaCollectionFactory   $productDeltaCollectionFactory
     */
    public function __construct(
        ProductDeltaRepositoryInterface $productDeltaRepository,
        ProductDeltaCollectionFactory $productDeltaCollectionFactory
    ) {
        $this->productDeltaRepository = $productDeltaRepository;
        $this->productDeltaCollectionFactory = $productDeltaCollectionFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function get($page, $pageSize, $storeId, $sinceId, $maxId = null)
    {
        if (null === $maxId) {
            $maxId = $this->initCollection()->getMaxId();
        }

        $this
            ->validateSinceId($sinceId)
            ->initCollection()
            ->removeOldEvents($sinceId)
            ->initCollection()
            ->getSkus($sinceId, $maxId)
            ->setOrder()
            ->setPageSize($pageSize);

        var_dump($page, $pageSize, $storeId, $sinceId, $maxId);
        die();
    }

    /**
     * @return int
     */
    private function getMaxId()
    {
        return (int)$this->productDeltaCollection->getLastItem()->getData('product_delta_id');
    }

    /**
     * @return $this
     */
    private function initCollection()
    {
        $this->productDeltaCollection = $this->productDeltaCollectionFactory->create();

        return $this;
    }

    /**
     * @param int $beforeId
     *
     * @return $this
     */
    private function removeOldEvents($beforeId)
    {
        $oldEvents = $this->productDeltaCollection
            ->addFieldToFilter('product_delta_id', ['lteq' => $beforeId]);

        $oldEvents->walk('delete');

        return $this;
    }

    /**
     * @param int $minId
     * @param int $maxId
     *
     * @return $this
     */
    private function getSkus($minId, $maxId)
    {
        $this->productDeltaCollection
            ->addFieldToFilter('product_delta_id', ['gt' => $minId])
            ->addFieldToFilter('product_delta_id', ['lteq' => $maxId]);

        return $this;
    }

    /**
     * @return $this
     */
    private function setOrder()
    {
        $this->productDeltaCollection
            ->setOrder('event_id', DataCollection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * @param int $pageSize
     *
     * @return $this
     */
    private function setPageSize($pageSize)
    {
        $this->productDeltaCollection
            ->setPageSize($pageSize);

        return $this;
    }

    /**
     * @param int $sinceId
     *
     * @return $this
     * @throws WebApiException
     */
    private function validateSinceId($sinceId)
    {
        if ($this->productDeltaRepository->isSinceIdIsHigherThanAutoIncrement($sinceId)) {
            throw new WebApiException(
                __('sinceId is higher than auto-increment'),
                WebApiException::HTTP_NOT_ACCEPTABLE,
                WebApiException::HTTP_NOT_ACCEPTABLE
            );
        }

        return $this;
    }
}
