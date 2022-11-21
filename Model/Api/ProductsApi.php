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
use Exception;
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
     * @param StoreManagerInterface               $storeManager
     * @param ProductsApiResponseInterfaceFactory $productsApiResponseFactory
     * @param LinkFieldHelper                     $linkFieldHelper
     * @param ProductHelper                       $productHelper
     *
     * @throws Exception
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
     * Get
     *
     * @param int    $page
     * @param int    $pageSize
     * @param string $storeId
     *
     * @return ProductsApiResponseInterface
     * @throws WebApiException
     */
    public function get(int $page, int $pageSize, string $storeId): ProductsApiResponseInterface
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

        return $this->productsApiResponseFactory
            ->create()
            ->setCurrentPage($page)
            ->setLastPage($lastPageNumber)
            ->setPageSize($pageSize)
            ->setTotalCount($this->numberOfItems)
            ->setProducts($this->handleProducts($this->productHelper->getProductCollection()));
    }

    /**
     * InitCollection
     *
     * @return ProductsApi
     */
    protected function initCollection(): ProductsApi
    {
        $this->productHelper->initCollection();

        return $this;
    }

    /**
     * HandleIds
     *
     * @param int $page
     * @param int $pageSize
     *
     * @return ProductsApi
     */
    protected function handleIds(int $page, int $pageSize): ProductsApi
    {
        $page --;
        $page *= $pageSize;

        $data = $this->productHelper->handleIds($page, $pageSize);

        $this->numberOfItems = $data['numberOfItems'];
        $this->minId = $data['minId'];
        $this->maxId = $data['maxId'];

        return $this;
    }

    /**
     * GetPrices
     *
     * @return ProductsApi
     */
    protected function getPrices(): ProductsApi
    {
        $wheres = [
            ['entity_table.' . $this->linkField . ' >= ?', $this->minId],
            ['entity_table.' . $this->linkField . ' <= ?', $this->maxId],
        ];
        $this->productHelper->getPrices($this->websiteIds, $this->customerGroups, $wheres);

        return $this;
    }

    /**
     * HandleCategoryIds
     *
     * @return ProductsApi
     */
    protected function handleCategoryIds(): ProductsApi
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
     * HandleChildrenProductIds
     *
     * @return ProductsApi
     */
    protected function handleChildrenProductIds(): ProductsApi
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
     * HandleStockData
     *
     * @return ProductsApi
     */
    protected function handleStockData(): ProductsApi
    {
        $this->productHelper->getStockData(
            [
                ['entity_table.' . $this->linkField . ' >= ?', $this->minId],
                ['entity_table.' . $this->linkField . ' <= ?', $this->maxId],
            ]
        );

        return $this;
    }

    /**
     * HandleStatusData
     *
     * @return ProductsApi
     */
    protected function handleStatusData(): ProductsApi
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
     * HandleAttributes
     *
     * @return ProductsApi
     */
    private function handleAttributes(): ProductsApi
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
     * SetWhere
     *
     * @return ProductsApi
     */
    protected function setWhere(): ProductsApi
    {
        $this->productHelper->setWhere(
            $this->linkField,
            $this->minId,
            $this->maxId
        );

        return $this;
    }

    /**
     * SetOrder
     *
     * @return ProductsApi
     */
    protected function setOrder(): ProductsApi
    {
        $this->productHelper->setOrder(
            $this->linkField,
            DataCollection::SORT_ORDER_ASC
        );

        return $this;
    }
}
