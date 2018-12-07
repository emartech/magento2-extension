<?php

namespace Emartech\Emarsys\Model\Api;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Data\Collection as DataCollection;
use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as ProductAttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as ProductAttributeCollection;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\UrlFactory as ProductUrlFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Store\Model\Store;
use Magento\Framework\UrlInterface;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\ProductInterface;

use Emartech\Emarsys\Api\ProductsApiInterface;
use Emartech\Emarsys\Api\Data\ProductsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\ProductsApiResponseInterface;
use Emartech\Emarsys\Api\Data\ProductInterfaceFactory;
use Emartech\Emarsys\Api\Data\ImagesInterfaceFactory;
use Emartech\Emarsys\Api\Data\ImagesInterface;
use Emartech\Emarsys\Api\Data\ProductStoreDataInterfaceFactory;
use Emartech\Emarsys\Model\ResourceModel\Api\Category as CategoryResource;
use Emartech\Emarsys\Model\ResourceModel\Api\Product as ProductResource;

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
     * @var ProductAttributeCollectionFactory
     */
    private $productAttributeCollectionFactory;

    /**
     * @var ProductAttributeCollection
     */
    private $productAttributeCollection;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var array
     */
    private $storeProductAttributeCodes = [
        'name',
        'price',
        'url_key',
        'description',
        'status',
        'store_id',
        'currency',
        'display_price',
        'special_price',
        'special_from_date',
        'special_to_date',
    ];

    /**
     * @var array
     */
    private $globalProductAttributeCodes = [
        'entity_id',
        'type',
        'children_entity_ids',
        'categories',
        'sku',
        'images',
        'qty',
        'is_in_stock',
        'stores',
        'image',
        'small_image',
        'thumbnail',
    ];

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
     * @var string
     */
    private $linkField = '';

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var CategoryResource
     */
    private $categoryResource;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * ProductsApi constructor.
     *
     * @param CategoryCollectionFactory           $categoryCollectionFactory
     * @param StoreManagerInterface               $storeManager
     * @param ScopeConfigInterface                $scopeConfig
     * @param ProductCollectionFactory            $productCollectionFactory
     * @param ProductsApiResponseInterfaceFactory $productsApiResponseFactory
     * @param ProductAttributeCollectionFactory   $productAttributeCollectionFactory
     * @param ProductAttributeCollection          $productAttributeCollection
     * @param ProductInterfaceFactory             $productFactory
     * @param ImagesInterfaceFactory              $imagesFactory
     * @param ProductStoreDataInterfaceFactory    $productStoreDataFactory
     * @param ProductUrlFactory                   $productUrlFactory
     * @param LoggerInterface                     $logger
     * @param MetadataPool                        $metadataPool
     * @param CategoryResource                    $categoryResource
     * @param ProductResource                     $productResource
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ProductCollectionFactory $productCollectionFactory,
        ProductsApiResponseInterfaceFactory $productsApiResponseFactory,
        ProductAttributeCollectionFactory $productAttributeCollectionFactory,
        ProductAttributeCollection $productAttributeCollection,
        ProductInterfaceFactory $productFactory,
        ImagesInterfaceFactory $imagesFactory,
        ProductStoreDataInterfaceFactory $productStoreDataFactory,
        ProductUrlFactory $productUrlFactory,
        LoggerInterface $logger,
        MetadataPool $metadataPool,
        CategoryResource $categoryResource,
        ProductResource $productResource
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->productUrlFactory = $productUrlFactory;

        $this->productCollectionFactory = $productCollectionFactory;
        $this->productsApiResponseFactory = $productsApiResponseFactory;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->productAttributeCollection = $productAttributeCollection;

        $this->productFactory = $productFactory;
        $this->imagesFactory = $imagesFactory;
        $this->productStoreDataFactory = $productStoreDataFactory;
        $this->logger = $logger;

        $this->metadataPool = $metadataPool;
        $this->categoryResource = $categoryResource;
        $this->productResource = $productResource;
    }

    /**
     * @param int    $page
     * @param int    $pageSize
     * @param string $storeId
     *
     * @return ProductsApiResponseInterface
     * @throws WebApiException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Statement_Exception
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
            ->joinData()
            ->setWhere()
            ->setOrder();

        $lastPageNumber = ceil($this->numberOfItems / $pageSize);

        return $this->productsApiResponseFactory->create()->setCurrentPage($page)
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
        $this->productCollection = $this->productCollectionFactory->create();

        $this->linkField = 'entity_id';//$this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();

        return $this;
    }

    /**
     * @param int $page
     * @param int $pageSize
     *
     * @return $this
     * @throws \Zend_Db_Statement_Exception
     */
    // @codingStandardsIgnoreLine
    protected function handleIds($page, $pageSize)
    {
        $page--;
        $page *= $pageSize;

        $data = $this->productResource->handleIds($page, $pageSize, $this->linkField);

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
        $this->childrenProductIds = $this->productResource->getChildrenProductIds($this->minId, $this->maxId);

        return $this;
    }

    /**
     * @return $this
     */
    // @codingStandardsIgnoreLine
    protected function handleStockData()
    {
        $this->stockData = $this->productResource->getStockData($this->minId, $this->maxId);

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    // @codingStandardsIgnoreLine
    protected function joinData()
    {
        $this->productAttributeCollection = $this->productAttributeCollectionFactory->create();
        $this->productAttributeCollection
            ->addFieldToFilter('attribute_code', [
                'in' => array_values(array_merge(
                    $this->storeProductAttributeCodes,
                    $this->globalProductAttributeCodes
                )),
            ]);

        $mainTableName = $this->productCollection->getMainTable();

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $productAttribute */
        foreach ($this->productAttributeCollection as $productAttribute) {
            if ($productAttribute->getBackendTable() === $mainTableName) {
                $this->productCollection->addAttributeToSelect($productAttribute->getAttributeCode());
            } elseif (in_array($productAttribute->getAttributeCode(), $this->globalProductAttributeCodes)) {
                $valueAlias = $this->getAttributeValueAlias($productAttribute->getAttributeCode());

                $this->productCollection->joinAttribute(
                    $valueAlias,
                    'catalog_product/' . $productAttribute->getAttributeCode(),
                    $this->linkField,
                    null,
                    'left'
                );
            } else {
                foreach (array_keys($this->storeIds) as $storeId) {
                    $valueAlias = $this->getAttributeValueAlias($productAttribute->getAttributeCode(), $storeId);

                    $this->productCollection->joinAttribute(
                        $valueAlias,
                        'catalog_product/' . $productAttribute->getAttributeCode(),
                        $this->linkField,
                        null,
                        'left',
                        $storeId
                    );
                }
            }
        }

        return $this;
    }

    /**
     * @param string   $attributeCode
     * @param int|null $storeId
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function getAttributeValueAlias($attributeCode, $storeId = null)
    {
        $returnValue = $attributeCode;
        if ($storeId !== null) {
            $returnValue .= '_' . $storeId;
        }
        return $returnValue;
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
                ->setEntityId($product->getId())
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
        if (array_key_exists($product->getId(), $this->stockData)) {
            return $this->stockData[$product->getId()]['is_in_stock'];
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
        if (array_key_exists($product->getId(), $this->stockData)) {
            return $this->stockData[$product->getId()]['qty'];
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

        $image = $product->getImage();
        if ($image) {
            $image = $imagePreUrl . $image;
        }

        $smallImage = $product->getSmallImage();
        if ($smallImage) {
            $smallImage = $imagePreUrl . $smallImage;
        }

        $thumbnail = $product->getThumbnail();
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
        if (array_key_exists($product->getId(), $this->childrenProductIds)) {
            return $this->childrenProductIds[$product->getId()];
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
        if (array_key_exists($product->getId(), $this->categoryIds)) {
            return $this->categoryIds[$product->getId()];
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
        $returnArray = [];

        foreach ($this->storeIds as $storeId => $storeObject) {
            $returnArray[] = $this->productStoreDataFactory->create()
                ->setStoreId($storeId)
                ->setStatus($product->getData($this->getAttributeValueAlias('status', $storeId)))
                ->setDescription($product->getData($this->getAttributeValueAlias('description', $storeId)))
                ->setLink($this->handleLink($product, $storeObject))
                ->setName($product->getData($this->getAttributeValueAlias('name', $storeId)))
                ->setPrice($this->handlePrice($product, $storeObject))
                ->setDisplayPrice($this->handleDisplayPrice($product, $storeObject))
                ->setCurrencyCode($this->getCurrencyCode($storeObject));
        }

        return $returnArray;
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
        $link = $product->getData($this->getAttributeValueAlias('url_key', $store->getId()));

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
     * @param Product $product
     * @param Store   $store
     *
     * @return int | float
     */
    // @codingStandardsIgnoreLine
    protected function handleDisplayPrice($product, $store)
    {
        $price = $product->getData($this->getAttributeValueAlias('price', $store->getId()));
        if (empty($price)) {
            $price = $product->getData($this->getAttributeValueAlias('price', 0));
        }

        $product->setPrice($price);
        $price = $product->getFinalPrice();

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

    /**
     * @param Product $product
     * @param Store   $store
     *
     * @return int | float
     */
    // @codingStandardsIgnoreLine
    protected function handlePrice($product, $store)
    {
        $price = $product->getData($this->getAttributeValueAlias('price', $store->getId()));
        $specialPrice = $product->getData($this->getAttributeValueAlias('special_price', $store->getId()));

        if (!empty($specialPrice)) {
            $specialFromDate = $product->getData($this->getAttributeValueAlias('special_from_date', $store->getId()));
            $specialToDate = $product->getData($this->getAttributeValueAlias('special_to_date', $store->getId()));

            if ($specialFromDate) {
                $specialFromDate = strtotime($specialFromDate);
            } else {
                $specialFromDate = false;
            }

            if ($specialToDate) {
                $specialToDate = strtotime($specialToDate);
            } else {
                $specialToDate = false;
            }

            if (($specialFromDate === false || $specialFromDate <= time()) &&
                ($specialToDate === false || $specialToDate >= time())
            ) {
                $price = $specialPrice;
            }
        }

        return $price;
    }
}
