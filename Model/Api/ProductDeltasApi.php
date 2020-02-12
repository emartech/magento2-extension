<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\ProductDeltasApiResponseInterface;
use Emartech\Emarsys\Api\Data\ProductDeltasApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\ProductDeltaRepositoryInterface;
use Emartech\Emarsys\Api\ProductDeltasApiInterface;
use Emartech\Emarsys\Helper\LinkField as LinkFieldHelper;
use Emartech\Emarsys\Helper\Product as ProductHelper;
use Emartech\Emarsys\Model\ResourceModel\ProductDelta\Collection as ProductDeltaCollection;
use Emartech\Emarsys\Model\ResourceModel\ProductDelta\CollectionFactory as ProductDeltaCollectionFactory;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Store\Model\StoreManagerInterface;

class ProductDeltasApi extends BaseProductsApi implements ProductDeltasApiInterface
{
    /**
     * @var ProductDeltasApiResponseInterfaceFactory
     */
    private $productDeltasApiResponseFactory;

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
     * @var null|string
     */
    private $mainTable = null;

    /**
     * ProductDeltasApi constructor.
     *
     * @param StoreManagerInterface                    $storeManager
     * @param ProductDeltaRepositoryInterface          $productDeltaRepository
     * @param ProductDeltaCollectionFactory            $productDeltaCollectionFactory
     * @param ProductDeltasApiResponseInterfaceFactory $productDeltasApiResponseFactory
     * @param ProductHelper                            $productHelper
     * @param LinkFieldHelper                          $linkFieldHelper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductDeltaRepositoryInterface $productDeltaRepository,
        ProductDeltaCollectionFactory $productDeltaCollectionFactory,
        ProductDeltasApiResponseInterfaceFactory $productDeltasApiResponseFactory,
        ProductHelper $productHelper,
        LinkFieldHelper $linkFieldHelper
    ) {
        parent::__construct(
            $storeManager,
            $productHelper,
            $linkFieldHelper
        );

        $this->productDeltasApiResponseFactory = $productDeltasApiResponseFactory;
        $this->productDeltaRepository = $productDeltaRepository;
        $this->productDeltaCollectionFactory = $productDeltaCollectionFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function get($page, $pageSize, $storeId, $sinceId, $maxId = null)
    {
        $this
            ->validateSinceId($sinceId)
            ->initStores($storeId);

        if (null === $maxId) {
            $maxId = $this->getMaxId();
        }

        $this
            ->removeOldEvents($sinceId)
            ->handleIds($sinceId, $maxId, $page, $pageSize)
            ->getPrices()
            ->handleCategoryIds()
            ->handleChildrenProductIds();

        return $this->productDeltasApiResponseFactory->create()
            ->setCurrentPage($page)
            ->setLastPage(0)
            ->setPageSize($pageSize)
            ->setTotalCount(1000)
            ->setProducts([])
            ->setMaxId($maxId);
    }

    /**
     * @return string
     */
    private function getMainTable()
    {
        if (null === $this->mainTable) {
            $this->mainTable = $this->productDeltaCollectionFactory->create()->getMainTable();
        }

        return $this->mainTable;
    }

    /**
     * @return int
     */
    private function getMaxId()
    {
        return $this->productDeltaCollectionFactory->create()
            ->getLastItem()->getData('product_delta_id');
    }

    /**
     * @return $this
     */
    private function initCollection()
    {
        $this->productDeltaCollection = $this->productDeltaCollectionFactory->create();

        return $this;
    }

    // @codingStandardsIgnoreLine
    protected function handleIds($minDeltaId, $maxDeltaId, $page, $pageSize)
    {
        $page--;
        $page *= $pageSize;

        $data = $this->productHelper->handleIds(
            $page,
            $pageSize,
            $this->getMainTable(),
            'product_delta_id',
            [
                ['product_delta_id >= ?', $minDeltaId],
                ['product_delta_id <= ?', $maxDeltaId],
            ],
            'entity_id'
        );

        $this->numberOfItems = $data['numberOfItems'];
        $this->minId = $data['minId'];
        $this->maxId = $data['maxId'];

        return $this;
    }

    /**
     * @param int $beforeId
     *
     * @return $this
     */
    private function removeOldEvents($beforeId)
    {
        $oldEvents = $this->productDeltaCollectionFactory->create()
            ->addFieldToFilter('product_delta_id', ['lteq' => $beforeId]);

        $oldEvents->walk('delete');

        return $this;
    }

    /**
     * @return $this
     */
    protected function getPrices()
    {
        $this->productHelper->getPrices(
            $this->websiteIds,
            $this->customerGroups,
            [],
            [
                ['pdt' => $this->getMainTable()],
                '{TABLE}.entity_id = pdt.entity_id AND pdt.product_delta_id BETWEEN ' . $this->minId . ' AND ' . $this->maxId,
                [],
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleCategoryIds()
    {
        $this->productHelper->getCategoryIds(
            [],
            [
                ['pdt' => $this->getMainTable()],
                'product_id = pdt.' . $this->linkField . ' AND pdt.product_delta_id BETWEEN ' . $this->minId . ' AND ' . $this->maxId,
                [],
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleChildrenProductIds()
    {
        $this->productHelper->getChildrenProductIds(
            [],
            [
                ['pdt' => $this->getMainTable()],
                'parent_id = pdt.' . $this->linkField . ' AND pdt.product_delta_id BETWEEN ' . $this->minId . ' AND ' . $this->maxId,
                [],
            ]
        );

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
