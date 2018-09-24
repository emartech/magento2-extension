<?php

namespace Emartech\Emarsys\Model\Api;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\Data\Collection as DataCollection;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

use Emartech\Emarsys\Api\ProductsApiInterface;
use Emartech\Emarsys\Api\Data\ProductsApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\ProductsApiResponseInterface;
use Emartech\Emarsys\Api\Data\ProductInterfaceFactory;
use Emartech\Emarsys\Api\Data\ImagesInterfaceFactory;
use Emartech\Emarsys\Api\Data\ImagesInterface;
use Emartech\Emarsys\Api\Data\ProductStoreDataInterfaceFactory;

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
     * @var ContainerBuilder
     */
    private $containerBuilder;

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
     * @var array
     */
    private $categories = [];

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
     * ProductsApi constructor.
     *
     * @param CategoryCollectionFactory           $categoryCollectionFactory
     * @param StoreManagerInterface               $storeManager
     * @param ContainerBuilder                    $containerBuilder
     * @param ScopeConfigInterface                $scopeConfig
     * @param ProductCollectionFactory            $productCollectionFactory
     * @param ProductsApiResponseInterfaceFactory $productsApiResponseFactory
     * @param ProductAttributeCollectionFactory   $productAttributeCollectionFactory
     * @param ProductAttributeCollection          $productAttributeCollection
     * @param ProductInterfaceFactory             $productFactory
     * @param ImagesInterfaceFactory              $imagesFactory
     * @param ProductStoreDataInterfaceFactory    $productStoreDataFactory
     * @param ProductUrlFactory                   $productUrlFactory
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        ContainerBuilder $containerBuilder,
        ScopeConfigInterface $scopeConfig,
        ProductCollectionFactory $productCollectionFactory,
        ProductsApiResponseInterfaceFactory $productsApiResponseFactory,
        ProductAttributeCollectionFactory $productAttributeCollectionFactory,
        ProductAttributeCollection $productAttributeCollection,
        ProductInterfaceFactory $productFactory,
        ImagesInterfaceFactory $imagesFactory,
        ProductStoreDataInterfaceFactory $productStoreDataFactory,
        ProductUrlFactory $productUrlFactory
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        $this->scopeConfig = $scopeConfig;
        $this->containerBuilder = $containerBuilder;
        $this->storeManager = $storeManager;
        $this->productUrlFactory = $productUrlFactory;

        $this->productCollectionFactory = $productCollectionFactory;
        $this->productsApiResponseFactory = $productsApiResponseFactory;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->productAttributeCollection = $productAttributeCollection;

        $this->productFactory = $productFactory;
        $this->imagesFactory = $imagesFactory;
        $this->productStoreDataFactory = $productStoreDataFactory;
    }

    /**
     * @param int    $page
     * @param int    $pageSize
     * @param string $storeId
     *
     * @return ProductsApiResponseInterface
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
            ->joinData()
            ->joinStock()
            ->joinCategories()
            ->joinChildrenProductIds()
            ->setOrder()
            ->setPage($page, $pageSize);

        $lastPageNumber = $this->productCollection->getLastPageNumber();
        $pageSize = $this->productCollection->getPageSize();
        $totalCount = $this->productCollection->getSize();

        $this->setGroupBy();

        return $this->productsApiResponseFactory->create()->setCurrentPage($this->productCollection->getCurPage())
            ->setLastPage($lastPageNumber)
            ->setPageSize($pageSize)
            ->setTotalCount($totalCount)
            ->setProducts($this->handleProducts());
    }

    /**
     * @param mixed $storeIds
     *
     * @return $this
     */
    private function initStores($storeIds)
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
     */
    private function initCollection()
    {
        $this->productCollection = $this->productCollectionFactory->create();

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function joinData()
    {
        $this->productAttributeCollection = $this->productAttributeCollectionFactory->create();
        $this->productAttributeCollection
            ->addFieldToFilter('attribute_code', [
                'in' => array_values(array_merge(
                    $this->storeProductAttributeCodes,
                    $this->globalProductAttributeCodes
                )),
            ]);

        $mainTableName = $this->productCollection->getResource()->getTable('catalog_product_entity');

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $productAttribute */
        foreach ($this->productAttributeCollection as $productAttribute) {
            if ($productAttribute->getBackendTable() === $mainTableName) {
                $this->productCollection->addAttributeToSelect($productAttribute->getAttributeCode());
            } elseif (in_array($productAttribute->getAttributeCode(), $this->globalProductAttributeCodes)) {
                $tableAlias = 'table_' . $productAttribute->getAttributeId();
                $valueAlias = $this->getAttributeValueAlias($productAttribute->getAttributeCode());

                $this->productCollection->joinTable(
                    [$tableAlias => $productAttribute->getBackendTable()],
                    'entity_id = entity_id',
                    [$valueAlias => 'value'],
                    ['attribute_id' => $productAttribute->getAttributeId()],
                    'left'
                );
            } else {
                foreach (array_keys($this->storeIds) as $storeId) {
                    $tableAlias = 'table_' . $productAttribute->getAttributeId() . '_' . $storeId;
                    $valueAlias = $this->getAttributeValueAlias($productAttribute->getAttributeCode(), $storeId);

                    $this->productCollection->joinTable(
                        [$tableAlias => $productAttribute->getBackendTable()],
                        'entity_id = entity_id',
                        [$valueAlias => 'value'],
                        ['store_id' => $storeId, 'attribute_id' => $productAttribute->getAttributeId()],
                        'left'
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
    private function getAttributeValueAlias($attributeCode, $storeId = null)
    {
        $returnValue = $attributeCode;
        if ($storeId !== null) {
            $returnValue .= '_' . $storeId;
        }
        return $returnValue;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function joinStock()
    {
        $this->productCollection->joinTable(
            $this->productCollection->getResource()->getTable('cataloginventory_stock_item'),
            'product_id = entity_id',
            ['qty', 'is_in_stock'],
            '{{table}}.stock_id=1',
            'left'
        );

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function joinCategories()
    {
        $this->productCollection->joinTable(
            $this->productCollection->getResource()->getTable('catalog_category_product'),
            'product_id = entity_id',
            ['category_ids' => 'GROUP_CONCAT(catalog_category_product.category_id)'],
            null,
            'left'
        );

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function joinChildrenProductIds()
    {
        $this->productCollection->joinTable(
            $this->productCollection->getResource()->getTable('catalog_product_super_link'),
            'parent_id = entity_id',
            ['children_ids' => 'GROUP_CONCAT(catalog_product_super_link.product_id)'],
            null,
            'left'
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function setOrder()
    {
        $this->productCollection
            ->setOrder('entity_id', DataCollection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * @return $this
     */
    private function setGroupBy()
    {
        $this->productCollection
            ->groupByAttribute('entity_id');

        return $this;
    }

    /**
     * @param int $page
     * @param int $pageSize
     *
     * @return $this
     */
    private function setPage($page, $pageSize)
    {
        $this->productCollection
            ->setCurPage($page)
            ->setPageSize($pageSize);

        return $this;
    }

    /**
     * @return array
     */
    private function handleProducts()
    {
        $returnArray = [];

        foreach ($this->productCollection as $product) {
            $returnArray[] = $this->productFactory->create()->setType($product->getTypeId())
                ->setCategories($this->handleCategories($product))
                ->setChildrenEntityIds($this->handleChildrenEntityIds($product))
                ->setEntityId($product->getId())
                ->setIsInStock($product->getIsInStock())
                ->setQty($product->getQty())
                ->setSku($product->getSku())
                ->setImages($this->handleImages($product))
                ->setStoreData($this->handleProductStoreData($product));
        }

        return $returnArray;
    }

    /**
     * @param Product $product
     *
     * @return ImagesInterface
     */
    private function handleImages($product)
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
    private function handleChildrenEntityIds($product)
    {
        $childrenIds = $product->getData('children_ids');
        if ($childrenIds) {
            return array_unique(explode(',', $childrenIds));
        }

        return [];
    }

    /**
     * @param Product $product
     *
     * @return array
     */
    private function handleCategories($product)
    {
        $returnArray = [];

        $categoryIds = $product->getData('category_ids');
        if ($categoryIds) {
            $categoryIds = explode(',', $categoryIds);

            foreach ($categoryIds as $categoryId) {
                $returnArray[] = $this->handleCategory($categoryId);
            }
        }

        return array_unique($returnArray);
    }

    /**
     * @param int $categoryId
     *
     * @return string
     */
    private function handleCategory($categoryId)
    {
        $categoryData = $this->getCategory($categoryId);

        if ($categoryData instanceof Category) {
            return $categoryData->getPath();
        }

        return '';
    }

    /**
     * @param int $categoryId
     *
     * @return Category | null
     */
    private function getCategory($categoryId)
    {
        if (!array_key_exists($categoryId, $this->categories)) {
            $categoryCollection = $this->categoryCollectionFactory->create();
            foreach ($categoryCollection as $category) {
                $this->categories[$category->getId()] = $category;
            }
        }

        return $this->categories[$categoryId];
    }

    /**
     * @param int $storeId
     *
     * @return string
     */
    private function getProductUrlSuffix($storeId)
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
    private function handleProductStoreData($product)
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
    private function handleLink($product, $store)
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
    private function getCurrencyCode($store)
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
    private function handleDisplayPrice($product, $store)
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
    private function handlePrice($product, $store)
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
