<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\ProductsApiResponseInterface;
use Emartech\Emarsys\Api\Data\ProductsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\ProductsApiInterface;
use Emartech\Emarsys\Helper\LinkField;
use Emartech\Emarsys\Helper\Product as ProductHelper;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Store\Model\StoreManagerInterface;

class ProductsApi implements ProductsApiInterface
{

    /**
     * @var ProductsApiResponseInterfaceFactory
     */
    private $productsApiResponseFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var int[]
     */
    private $storeIds = [];

    /**
     * @var array
     */
    private $websiteIds = [];

    /**
     * @var int[]
     */
    private $customerGroups = [0];

    /**
     * @var int
     */
    private $minId = 0;

    /**
     * @var int
     */
    private $maxId = 0;

    /**
     * @var int
     */
    private $numberOfItems = 0;

    /**
     * @var string
     */
    private $linkField;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var LinkField
     */
    private $linkFieldHelper;

    /**
     * @var ProductHelper
     */
    private $productHelper;

    /**
     * ProductsApi constructor.
     *
     * @param StoreManagerInterface               $storeManager
     * @param ScopeConfigInterface                $scopeConfig
     * @param ProductsApiResponseInterfaceFactory $productsApiResponseFactory
     * @param ObjectManagerInterface              $objectManager
     * @param LinkField                           $linkFieldHelper
     * @param ProductHelper                       $productHelper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ProductsApiResponseInterfaceFactory $productsApiResponseFactory,
        ObjectManagerInterface $objectManager,
        LinkField $linkFieldHelper,
        ProductHelper $productHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->productsApiResponseFactory = $productsApiResponseFactory;
        $this->objectManager = $objectManager;
        $this->linkFieldHelper = $linkFieldHelper;
        $this->linkField = $this->linkFieldHelper->getEntityLinkField(ProductInterface::class);
        $this->productHelper = $productHelper;
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
            ->initStores($storeId);

        if (!array_key_exists(0, $this->storeIds)) {
            throw new WebApiException(__('Store ID must contain 0'));
        }

        $this
            ->initCollection()
            ->handleIds($page, $pageSize)
            ->getPrices()
            ->handleCategoryIds()
            ->handleChildrenProductIds()
            ->handleStockData()
            ->handleAttributes()
            ->setWhere()
            ->setOrder();

        $lastPageNumber = ceil($this->numberOfItems / $pageSize);

        return $this->productsApiResponseFactory->create()
            ->setCurrentPage($page)
            ->setLastPage($lastPageNumber)
            ->setPageSize($pageSize)
            ->setTotalCount($this->numberOfItems)
            ->setProducts($this->handleProducts());
    }

    /**
     * @param mixed $storeIds
     *
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function initStores($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = explode(',', $storeIds);
        }

        $availableStores = $this->storeManager->getStores(true);

        foreach ($availableStores as $availableStore) {
            $storeId = (int)$availableStore->getId();
            if (in_array($storeId, $storeIds)) {
                $this->storeIds[$storeId] = $availableStore;
                $websiteId = (int)$availableStore->getWebsiteId();
                if ($websiteId) {
                    if (!array_key_exists($websiteId, $this->websiteIds)) {
                        $this->websiteIds[$websiteId] = [];
                    }
                    $this->websiteIds[$websiteId][] = $storeId;
                }
            }
        }

        return $this;
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
        $this->productHelper->getPrices($this->websiteIds, $this->customerGroups, $this->minId, $this->maxId);

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleCategoryIds()
    {
        $this->productHelper->getCategoryIds($this->minId, $this->maxId);

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleChildrenProductIds()
    {
        $this->productHelper->getChildrenProductIds($this->minId, $this->maxId);

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleStockData()
    {
        $this->productHelper->getStockData($this->minId, $this->maxId, $this->linkField);

        return $this;
    }

    /**
     * @return $this
     */
    private function handleAttributes()
    {
        $this->productHelper->getAttributeData(
            $this->minId,
            $this->maxId,
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
        $this->productHelper->setWhere($this->linkField, $this->minId, $this->maxId);

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function setOrder()
    {
        $this->productHelper->setOrder($this->linkField, DataCollection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * @return array
     */
    // @codingStandardsIgnoreLine
    protected function handleProducts()
    {
        $returnArray = [];

        /** @var Product $product */
        foreach ($this->productHelper->getProductCollection() as $product) {
            $returnArray[] = $this->productHelper->buildProductObject($product, $this->storeIds, $this->linkField);
        }

        return $returnArray;
    }
}
