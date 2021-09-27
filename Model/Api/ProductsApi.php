<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\ProductsApiResponseInterface;
use Emartech\Emarsys\Api\Data\ProductsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\ProductsApiInterface;
use Emartech\Emarsys\Helper\LinkField as LinkFieldHelper;
use Emartech\Emarsys\Helper\Product as ProductHelper;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Store\Model\StoreManagerInterface;

class ProductsApi extends BaseProductsApi implements ProductsApiInterface
{
    /**
     * @var ProductsApiResponseInterfaceFactory
     */
    private $productsApiResponseFactory;

    /**
     * ProductsApi constructor.
     *
     * @param StoreManagerInterface               $storeManager
     * @param ProductsApiResponseInterfaceFactory $productsApiResponseFactory
     * @param LinkFieldHelper                     $linkFieldHelper
     * @param ProductHelper                       $productHelper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductsApiResponseInterfaceFactory $productsApiResponseFactory,
        LinkFieldHelper $linkFieldHelper,
        ProductHelper $productHelper
    ) {
        parent::__construct(
            $storeManager,
            $productHelper,
            $linkFieldHelper
        );

        $this->productsApiResponseFactory = $productsApiResponseFactory;
    }

    /**
     * @param int    $page
     * @param int    $pageSize
     * @param string $storeId
     *
     * @return ProductsApiResponseInterface
     * @throws WebApiException
     */
    public function get($page, $pageSize, $storeId)
    {
        $this
            ->initStores($storeId)
            ->initCollection()
            ->handleIds($page, $pageSize)
            ->getPrices()
            ->handleCategoryIds()
            ->handleChildrenProductIds()
            ->handleStockData()
            ->handleStatusData()
            ->handleAttributes()
            ->setWhere()
            ->setOrder();

        $lastPageNumber = ceil($this->numberOfItems / $pageSize);

        return $this->productsApiResponseFactory->create()
            ->setCurrentPage($page)
            ->setLastPage($lastPageNumber)
            ->setPageSize($pageSize)
            ->setTotalCount($this->numberOfItems)
            ->setProducts(
                $this->handleProducts(
                    $this->productHelper->getProductCollection()
                )
            );
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function initCollection()
    {
        $this->productHelper->initCollection();

        return $this;
    }

    /**
     * @param int $page
     * @param int $pageSize
     *
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleIds($page, $pageSize)
    {
        $page--;
        $page *= $pageSize;

        $data = $this->productHelper->handleIds($page, $pageSize);

        $this->numberOfItems = $data['numberOfItems'];
        $this->minId = $data['minId'];
        $this->maxId = $data['maxId'];

        return $this;
    }

    /**
     * @return $this
     */
    protected function getPrices()
    {
        $wheres = [
            ['entity_table.' . $this->linkField . ' >= ?', $this->minId],
            ['entity_table.' . $this->linkField . ' <= ?', $this->maxId],
        ];
        $this->productHelper->getPrices(
            $this->websiteIds,
            $this->customerGroups,
            $wheres
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
            [
                ['entity_table.' . $this->linkField . ' >= ?', $this->minId],
                ['entity_table.' . $this->linkField . ' <= ?', $this->maxId],
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
            [
                ['parent_id >= ?', $this->minId],
                ['parent_id <= ?', $this->maxId],
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleStockData()
    {
        $this->productHelper->getStockData(
            [
                ['entity_table.' . $this->linkField . ' >= ?', $this->minId],
                ['entity_table.' . $this->linkField . ' <= ?', $this->maxId],
            ]
        );

        return $this;
    }

    protected function handleStatusData()
    {
        $this->productHelper->getStatusData(
            [
                ['entity_table.' . $this->linkField . ' >= ?', $this->minId],
                ['entity_table.' . $this->linkField . ' <= ?', $this->maxId],
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function handleAttributes()
    {
        $this->productHelper->getAttributeData(
            [
                [$this->linkField . ' >= ?', $this->minId],
                [$this->linkField . ' <= ?', $this->maxId],
            ],
            array_keys($this->storeIds)
        );

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function setWhere()
    {
        $this->productHelper->setWhere(
            $this->linkField,
            $this->minId,
            $this->maxId
        );

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function setOrder()
    {
        $this->productHelper->setOrder(
            $this->linkField,
            DataCollection::SORT_ORDER_ASC
        );

        return $this;
    }
}
