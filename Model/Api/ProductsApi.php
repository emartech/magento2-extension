<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\ImagesInterface;
use Emartech\Emarsys\Api\Data\ImagesInterfaceFactory;
use Emartech\Emarsys\Api\Data\ProductInterfaceFactory;
use Emartech\Emarsys\Api\Data\ProductsApiResponseInterface;
use Emartech\Emarsys\Api\Data\ProductsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\ProductStoreDataInterfaceFactory;
use Emartech\Emarsys\Api\ProductsApiInterface;
use Emartech\Emarsys\Helper\LinkField;
use Emartech\Emarsys\Model\ResourceModel\Api\Category as CategoryResource;
use Emartech\Emarsys\Model\ResourceModel\Api\Product as ProductResource;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\UrlFactory as ProductUrlFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ProductsApi
 *
 * @package Emartech\Emarsys\Model\Api
 */
class ProductsApi implements ProductsApiInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductsApiResponseInterfaceFactory
     */
    private $productsApiResponseFactory;

    /**
     * @var ProductCollection
     */
    private $productCollection;

    /**
     * @var ProductInterfaceFactory
     */
    private $productFactory;

    /**
     * @var ImagesInterfaceFactory
     */
    private $imagesFactory;

    /**
     * @var ProductStoreDataInterfaceFactory
     */
    private $productStoreDataFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductUrlFactory
     */
    private $productUrlFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var array
     */
    private $productUrlSuffix = [];

    /**
     * @var array
     */
    private $storeIds = [];

    /**
     * @var int
     */
    private $websiteId = 0;

    /**
     * @var LoggerInterface
     */
    private $logger;

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
     * @var array
     */
    private $categoryIds = [];

    /**
     * @var array
     */
    private $childrenProductIds = [];

    /**
     * @var array
     */
    private $stockData = [];

    /**
     * @var array
     */
    private $attributeData = [];

    /**
     * @var string
     */
    private $linkField;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var CategoryResource
     */
    private $categoryResource;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var LinkField
     */
    private $linkFieldHelper;

    /**
     * ProductsApi constructor.
     *
     * @param CategoryCollectionFactory           $categoryCollectionFactory
     * @param StoreManagerInterface               $storeManager
     * @param ScopeConfigInterface                $scopeConfig
     * @param ProductCollectionFactory            $productCollectionFactory
     * @param ProductsApiResponseInterfaceFactory $productsApiResponseFactory
     * @param ProductInterfaceFactory             $productFactory
     * @param ImagesInterfaceFactory              $imagesFactory
     * @param ProductStoreDataInterfaceFactory    $productStoreDataFactory
     * @param ProductUrlFactory                   $productUrlFactory
     * @param LoggerInterface                     $logger
     * @param CategoryResource                    $categoryResource
     * @param ProductResource                     $productResource
     * @param ObjectManagerInterface              $objectManager
     * @param LinkField                           $linkFieldHelper
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ProductCollectionFactory $productCollectionFactory,
        ProductsApiResponseInterfaceFactory $productsApiResponseFactory,
        ProductInterfaceFactory $productFactory,
        ImagesInterfaceFactory $imagesFactory,
        ProductStoreDataInterfaceFactory $productStoreDataFactory,
        ProductUrlFactory $productUrlFactory,
        LoggerInterface $logger,
        CategoryResource $categoryResource,
        ProductResource $productResource,
        ObjectManagerInterface $objectManager,
        LinkField $linkFieldHelper
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->productUrlFactory = $productUrlFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productsApiResponseFactory = $productsApiResponseFactory;
        $this->productFactory = $productFactory;
        $this->imagesFactory = $imagesFactory;
        $this->productStoreDataFactory = $productStoreDataFactory;
        $this->logger = $logger;
        $this->categoryResource = $categoryResource;
        $this->productResource = $productResource;
        $this->objectManager = $objectManager;
        $this->linkFieldHelper = $linkFieldHelper;
        $this->linkField = $this->linkFieldHelper->getEntityLinkField(ProductInterface::class);
    }

    /**
     * @param int    $page
     * @param int    $pageSize
     * @param string $storeId
     *
     * @return ProductsApiResponseInterface
     * @throws WebApiException
     * @throws \Magento\Framework\Exception\LocalizedException
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
            if (in_array($availableStore->getId(), $storeIds)) {
                $this->storeIds[$availableStore->getId()] = $availableStore;
            }
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    // @codingStandardsIgnoreLine
    protected function initCollection()
    {
        $this->productCollection = $this->productCollectionFactory->create()
            ->addFinalPrice();
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

        $data = $this->productResource->handleIds($page, $pageSize);

        $this->numberOfItems = $data['numberOfItems'];
        $this->minId = $data['minId'];
        $this->maxId = $data['maxId'];

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleCategoryIds()
    {
        $this->categoryIds = $this->categoryResource->getCategoryIds($this->minId, $this->maxId);

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleChildrenProductIds()
    {
        $this->childrenProductIds = $this->productResource
            ->getChildrenProductIds($this->minId, $this->maxId);

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleStockData()
    {
        $this->stockData = $this->productResource->getStockData($this->minId, $this->maxId, $this->linkField);

        return $this;
    }

    /**
     * @return $this
     */
    private function handleAttributes()
    {
        $this->attributeData = $this->productResource
            ->getAttributeData($this->minId, $this->maxId, array_keys($this->storeIds));

        return $this;
    }

    // @codingStandardsIgnoreLine
    protected function setWhere()
    {
        $this->productCollection
            ->addFieldToFilter($this->linkField, ['from' => $this->minId])
            ->addFieldToFilter($this->linkField, ['to' => $this->maxId]);

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function setOrder()
    {
        $this->productCollection
            ->groupByAttribute($this->linkField)
            ->setOrder($this->linkField, DataCollection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * @return array
     */
    // @codingStandardsIgnoreLine
    protected function handleProducts()
    {
        $returnArray = [];

        foreach ($this->productCollection as $product) {
            $returnArray[] = $this->productFactory->create()->setType($product->getTypeId())
                ->setCategories($this->handleCategories($product))
                ->setChildrenEntityIds($this->handleChildrenEntityIds($product))
                ->setEntityId($product->getEntityId())
                ->setIsInStock($this->handleStock($product))
                ->setQty($this->handleQty($product))
                ->setSku($product->getSku())
                ->setImages($this->handleImages($product))
                ->setStoreData($this->handleProductStoreData($product));
        }

        return $returnArray;
    }

    /**
     * @param Product $product
     *
     * @return int
     */
    // @codingStandardsIgnoreLine
    protected function handleStock($product)
    {
        if (array_key_exists($product->getEntityId(), $this->stockData)) {
            return $this->stockData[$product->getEntityId()]['is_in_stock'];
        }

        return 0;
    }

    /**
     * @param Product $product
     *
     * @return int
     */
    // @codingStandardsIgnoreLine
    protected function handleQty($product)
    {
        if (array_key_exists($product->getData($this->linkField), $this->stockData)) {
            return $this->stockData[$product->getEntityId()]['qty'];
        }

        return 0;
    }

    /**
     * @param Product $product
     *
     * @return ImagesInterface
     */
    // @codingStandardsIgnoreLine
    protected function handleImages($product)
    {
        $imagePreUrl = $this->storeIds[0]->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';

        try {
            $image = $this->getStoreData($product->getData($this->linkField), 0, 'image');
        } catch (\Exception $e) {
            $image = null;
        }

        if ($image) {
            $image = $imagePreUrl . $image;
        }

        try {
            $smallImage = $this->getStoreData($product->getData($this->linkField), 0, 'small_image');
        } catch (\Exception $e) {
            $smallImage = null;
        }

        if ($smallImage) {
            $smallImage = $imagePreUrl . $smallImage;
        }

        try {
            $thumbnail = $this->getStoreData($product->getData($this->linkField), 0, 'thumbnail');
        } catch (\Exception $e) {
            $thumbnail = null;
        }

        if ($thumbnail) {
            $thumbnail = $imagePreUrl . $thumbnail;
        }

        return $this->imagesFactory->create()
            ->setImage($image)
            ->setSmallImage($smallImage)
            ->setThumbnail($thumbnail);
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    protected function handleChildrenEntityIds($product)
    {
        if (array_key_exists($product->getData($this->linkField), $this->childrenProductIds)) {
            return $this->childrenProductIds[$product->getData($this->linkField)];
        }

        return [];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    protected function handleCategories($product)
    {
        if (array_key_exists($product->getEntityId(), $this->categoryIds)) {
            return $this->categoryIds[$product->getEntityId()];
        }

        return [];
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function getProductUrlSuffix($storeId)
    {
        if (!isset($this->productUrlSuffix[$storeId])) {
            $this->productUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return $this->productUrlSuffix[$storeId];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    protected function handleProductStoreData($product)
    {
        $product->setPriceCalculation(false);

        $returnArray = [];

        foreach ($this->storeIds as $storeId => $storeObject) {
            $productId = $product->getData($this->linkField);

            $price = (float)$product->getFinalPrice();
            $displayPrice = (float)$this->getDisplayPrice($price, $storeObject);
            $originalPrice = (float)$product->getPrice();
            $originalDisplayPrice = (float)$this->getDisplayPrice($originalPrice, $storeObject);

            $returnArray[] = $this->productStoreDataFactory->create()
                ->setStoreId($storeId)
                ->setStatus($this->getStoreData($productId, $storeId, 'status'))
                ->setDescription($this->getStoreData($productId, $storeId, 'description'))
                ->setLink($this->handleLink($product, $storeObject))
                ->setName($this->getStoreData($productId, $storeId, 'name'))
                ->setPrice($price)
                ->setDisplayPrice($displayPrice)
                ->setOriginalPrice($originalPrice)
                ->setOriginalDisplayPrice($originalDisplayPrice)
                ->setCurrencyCode($this->getCurrencyCode($storeObject));
        }

        return $returnArray;
    }

    /**
     * @param int    $productId
     * @param int    $storeId
     * @param string $attributeCode
     *
     * @return string|null
     */
    private function getStoreData($productId, $storeId, $attributeCode)
    {
        if (array_key_exists($productId, $this->attributeData)
            && array_key_exists($storeId, $this->attributeData[$productId])
            && array_key_exists($attributeCode, $this->attributeData[$productId][$storeId])
        ) {
            return $this->attributeData[$productId][$storeId][$attributeCode];
        }

        if ($storeId != 0) {
            return $this->getStoreData($productId, 0, $attributeCode);
        }

        return null;
    }

    /**
     * @param Product $product
     * @param Store   $store
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function handleLink($product, $store)
    {
        $link = $this->getStoreData($product->getData($this->linkField), $store->getId(), 'url_key');

        if ($link) {
            return $store->getBaseUrl() . $link . $this->getProductUrlSuffix($store->getId());
        }

        return '';
    }

    /**
     * @param Store $store
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function getCurrencyCode($store)
    {
        if ($store->getId() === '0') {
            return $store->getBaseCurrencyCode();
        }
        return $store->getCurrentCurrencyCode();
    }

    /**
     * @param float $price
     * @param Store $store
     *
     * @return float
     */
    protected function getDisplayPrice($price, $store)
    {
        if ($this->getCurrencyCode($store) !== $store->getBaseCurrencyCode()) {
            try {
                $tmp = $store->getBaseCurrency()->convert($price, $store->getCurrentCurrencyCode());
                $price = $tmp;
            } catch (\Exception $e) {
                $this->logger->error($e);
            }
        }

        return $price;
    }
}
