<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Helper;

use DateTime;
use Emartech\Emarsys\Api\AttributesApiInterface;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\Data\ConfigInterfaceFactory;
use Emartech\Emarsys\Api\Data\ExtraFieldsInterfaceFactory;
use Emartech\Emarsys\Api\Data\ImagesInterface;
use Emartech\Emarsys\Api\Data\ImagesInterfaceFactory;
use Emartech\Emarsys\Api\Data\ProductInterface;
use Emartech\Emarsys\Api\Data\ProductInterfaceFactory;
use Emartech\Emarsys\Api\Data\ProductStoreDataInterface;
use Emartech\Emarsys\Api\Data\ProductStoreDataInterfaceFactory;
use Emartech\Emarsys\Model\ResourceModel\Api\Category as CategoryResource;
use Emartech\Emarsys\Model\ResourceModel\Api\Product as ProductResource;
use Exception;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Stdlib\DateTime\Filter\DateTime as DateTimeFilter;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class Product extends AbstractHelper
{
    /**
     * @var string[]
     */
    private $fields = [
        'entity_id',
        'type',
        'children_entity_ids',
        'categories',
        'sku',
        'images',
        'qty',
        'is_in_stock',
        'stores',
    ];

    /**
     * @var null|string[]
     */
    private $extraFields = null;

    /**
     * @var string[]
     */
    private $storeFields = [
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
        'image',
        'small_image',
        'thumbnail',
    ];

    /**
     * @var null|string[]
     */
    private $extraStoreFields = null;

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductCollection
     */
    private $productCollection;

    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var CategoryResource
     */
    private $categoryResource;

    /**
     * @var array
     */
    private $productAttributeData;

    /**
     * @var array
     */
    private $productAttributeValues;

    /**
     * @var ProductInterfaceFactory
     */
    private $productFactory;

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
    private $statusData = [];

    /**
     * @var array
     */
    private $productUrlSuffix = [];

    /**
     * @var ImagesInterfaceFactory
     */
    private $imagesFactory;

    /**
     * @var ProductStoreDataInterfaceFactory
     */
    private $productStoreDataFactory;

    /**
     * @var ExtraFieldsInterfaceFactory
     */
    private $extraFieldsFactory;

    /**
     * @var array
     */
    private $priceData = [];

    /**
     * @var DateTimeFilter
     */
    private $dateTimeFilter;

    /**
     * Product constructor.
     *
     * @param ConfigInterfaceFactory           $configFactory
     * @param ProductCollectionFactory         $productCollectionFactory
     * @param ProductResource                  $productResource
     * @param CategoryResource                 $categoryResource
     * @param ProductInterfaceFactory          $productFactory
     * @param ImagesInterfaceFactory           $imagesFactory
     * @param ProductStoreDataInterfaceFactory $productStoreDataFactory
     * @param ExtraFieldsInterfaceFactory      $extraFieldsFactory
     * @param DateTimeFilter                   $dateTimeFilter
     * @param Context                          $context
     */
    public function __construct(
        ConfigInterfaceFactory $configFactory,
        ProductCollectionFactory $productCollectionFactory,
        ProductResource $productResource,
        CategoryResource $categoryResource,
        ProductInterfaceFactory $productFactory,
        ImagesInterfaceFactory $imagesFactory,
        ProductStoreDataInterfaceFactory $productStoreDataFactory,
        ExtraFieldsInterfaceFactory $extraFieldsFactory,
        DateTimeFilter $dateTimeFilter,
        Context $context
    ) {
        $this->configFactory = $configFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productResource = $productResource;
        $this->categoryResource = $categoryResource;
        $this->productFactory = $productFactory;
        $this->imagesFactory = $imagesFactory;
        $this->productStoreDataFactory = $productStoreDataFactory;
        $this->extraFieldsFactory = $extraFieldsFactory;
        $this->dateTimeFilter = $dateTimeFilter;

        parent::__construct(
            $context
        );
    }

    /**
     * @return string[]
     */
    public function getProductGlobalFields()
    {
        return $this->fields;
    }

    /**
     * @return string[]
     */
    public function getProductStoreFields()
    {
        return $this->storeFields;
    }

    /**
     * @return string[]
     */
    public function getProductFields()
    {
        return array_merge(
            $this->getProductGlobalFields(),
            $this->getProductStoreFields()
        );
    }

    /**
     * @return string[]
     */
    public function getProductExtraFields()
    {
        if (null == $this->extraFields) {
            $this->extraFields = [];

            /** @var ConfigInterface $config */
            $config = $this->configFactory->create();

            $productAttributes = $config->getConfigValue(
                AttributesApiInterface::TYPE_PRODUCT . ConfigInterface::ATTRIBUTE_CONFIG_POST_TAG,
                0
            );

            if (is_array($productAttributes)) {
                $this->extraFields = $productAttributes;
            }
        }

        return $this->extraFields;
    }

    /**
     * @return $this
     */
    public function initCollection()
    {
        /** @var ProductCollection customerCollection */
        $this->productCollection = $this->productCollectionFactory->create();

        return $this;
    }

    /**
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $table
     * @param string|null $primaryKey
     * @param array       $wheres
     * @param string|null $countField
     *
     * @return array
     */
    public function handleIds(
        $page,
        $pageSize,
        $table = null,
        $primaryKey = null,
        $wheres = [],
        $countField = null
    ) {
        return $this->productResource->handleIds(
            $page,
            $pageSize,
            $table,
            $primaryKey,
            $wheres,
            $countField
        );
    }

    /**
     * @param array      $wheres
     * @param array|null $joinInner
     *
     * @return $this
     */
    public function getCategoryIds($wheres, $joinInner = null)
    {
        $this->categoryIds = $this->categoryResource->getCategoryIds(
            $wheres,
            $joinInner
        );

        return $this;
    }

    /**
     * @param array      $wheres
     * @param array|null $joinInner
     *
     * @return $this
     */
    public function getChildrenProductIds($wheres, $joinInner = null)
    {
        $this->childrenProductIds = $this->productResource->getChildrenProductIds(
            $wheres,
            $joinInner
        );

        return $this;
    }

    /**
     * @param array      $wheres
     * @param array|null $joinInner
     *
     * @return $this
     */
    public function getStockData($wheres, $joinInner = null)
    {
        $this->stockData = $this->productResource->getStockData(
            $wheres,
            $joinInner
        );

        return $this;
    }

    public function getStatusData($wheres, $joinInner = null)
    {
        $this->statusData = $this->productResource->getStatusData(
            $wheres,
            $joinInner
        );

        return $this;
    }

    /**
     * @param array         $wheres
     * @param int[]         $storeIds
     * @param array|null    $joinInner
     * @param null|string[] $fields
     */
    public function getAttributeData(
        $wheres,
        $storeIds,
        $joinInner = null,
        $fields = null
    ) {
        if (!$fields) {
            $fields = array_merge(
                $this->getProductFields(),
                $this->getProductExtraFields()
            );
        }

        $data = $this->productResource->getAttributeData(
            $wheres,
            $storeIds,
            $fields,
            $joinInner
        );
        if (isset($data['attribute_data'])) {
            $this->productAttributeData = $data['attribute_data'];
        }
        if (isset($data['attribute_values'])) {
            $this->productAttributeValues = $data['attribute_values'];
        }
    }

    /**
     * @param string $linkField
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function setWhere($linkField, $min, $max)
    {
        $this->productCollection
            ->addFieldToFilter($linkField, ['from' => $min])
            ->addFieldToFilter($linkField, ['to' => $max]);

        return $this;
    }

    /**
     * @param string $linkField
     * @param string $direction
     *
     * @return $this
     */
    public function setOrder($linkField, $direction)
    {
        $this->productCollection
            ->groupByAttribute($linkField)
            ->setOrder($linkField, $direction);

        return $this;
    }

    /**
     * @return ProductCollection
     */
    public function getProductCollection()
    {
        return $this->productCollection;
    }

    /**
     * @param ProductModel $product
     * @param array        $storeIds
     * @param string       $linkField
     * @param bool         $toArray
     *
     * @return ProductInterface
     */
    public function buildProductObject(
        $product,
        $storeIds,
        $linkField,
        $toArray = false
    ) {
        $productEntityId = $product->getEntityId();
        $productId = $product->getData($linkField);

        /** @var ProductInterface $productItem */
        $productItem = $this->productFactory
            ->create()
            ->setType($product->getTypeId())
            ->setCategories(
                $this->handleCategories(
                    $productEntityId
                )
            )
            ->setChildrenEntityIds(
                $this->handleChildrenEntityIds(
                    $productId
                )
            )
            ->setEntityId($productEntityId)
            ->setIsInStock(
                $this->handleStock(
                    $productEntityId
                )
            )
            ->setQty(
                $this->handleQty(
                    $productEntityId
                )
            )
            ->setSku($product->getSku())
            ->setImages(
                $this->handleImages(
                    $storeIds[0],
                    $productId
                )
            )
            ->setStoreData(
                $this->handleProductStoreData(
                    $product,
                    $storeIds,
                    $productId,
                    $productEntityId,
                    $toArray
                )
            );

        if ($toArray) {
            $productItem = $productItem->getData();
        }

        return $productItem;
    }

    /**
     * @param int $productId
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    protected function handleCategories($productId)
    {
        if (array_key_exists($productId, $this->categoryIds)) {
            return $this->categoryIds[$productId];
        }

        return [];
    }

    /**
     * @param int $productId
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    protected function handleChildrenEntityIds($productId)
    {
        if (array_key_exists($productId, $this->childrenProductIds)) {
            return $this->childrenProductIds[$productId];
        }

        return [];
    }

    /**
     * @param int $productId
     *
     * @return int
     */
    // @codingStandardsIgnoreLine
    protected function handleStock($productId)
    {
        if (array_key_exists($productId, $this->stockData)) {
            return $this->stockData[$productId]['is_in_stock'];
        }

        return 0;
    }

    /**
     * @param int $productId
     *
     * @return int
     */
    // @codingStandardsIgnoreLine
    protected function handleQty($productId)
    {
        if (array_key_exists($productId, $this->stockData)) {
            return $this->stockData[$productId]['qty'];
        }

        return 0;
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
        if (array_key_exists($productId, $this->productAttributeData)
            && array_key_exists(
                $storeId,
                $this->productAttributeData[$productId]
            )
            && array_key_exists(
                $attributeCode,
                $this->productAttributeData[$productId][$storeId]
            )
        ) {
            return $this->productAttributeData[$productId][$storeId][$attributeCode];
        }

        if ($storeId != 0) {
            return $this->getStoreData($productId, 0, $attributeCode);
        }

        return null;
    }

    /**
     * @param int    $storeId
     * @param string $field
     * @param string $value
     *
     * @return string
     */
    private function getStoreAttributeValue($storeId, $field, $value)
    {
        if (array_key_exists($storeId, $this->productAttributeValues) &&
            array_key_exists($field, $this->productAttributeValues[$storeId]) &&
            array_key_exists(
                $value,
                $this->productAttributeValues[$storeId][$field]
            )
        ) {
            return $this->productAttributeValues[$storeId][$field][$value];
        }

        if ($storeId != 0) {
            return $this->getStoreAttributeValue(0, $field, $value);
        }

        return null;
    }

    /**
     * @param Store $store
     * @param int   $id
     *
     * @return ImagesInterface
     */
    protected function handleImages($store, $id)
    {
        $imagePreUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';

        try {
            $image = $this->getStoreData($id, $store->getId(), 'image');
            $image = $imagePreUrl . $image;
        } catch (Exception $e) {
            $image = null;
        }

        try {
            $smallImage = $this->getStoreData(
                $id,
                $store->getId(),
                'small_image'
            );
            $smallImage = $imagePreUrl . $smallImage;
        } catch (Exception $e) {
            $smallImage = null;
        }

        try {
            $thumbnail = $this->getStoreData($id, $store->getId(), 'thumbnail');
            $thumbnail = $imagePreUrl . $thumbnail;
        } catch (Exception $e) {
            $thumbnail = null;
        }

        return $this->imagesFactory->create()
            ->setImage($image)
            ->setSmallImage($smallImage)
            ->setThumbnail($thumbnail);
    }

    /**
     * @param ProductModel $product
     * @param array        $storeIds
     * @param int          $productId
     * @param int          $productEntityId
     * @param bool         $toArray
     *
     * @return ProductStoreDataInterface[]
     */
    // @codingStandardsIgnoreLine
    protected function handleProductStoreData(
        $product,
        $storeIds,
        $productId,
        $productEntityId,
        $toArray = false
    ) {
        $product->setPriceCalculation(false);

        $returnArray = [];

        foreach ($storeIds as $storeId => $storeObject) {
            $price = $this->getPrice($productId, $storeId);

            $webShopPrice = (float)$this->getWebShopPrice(
                $productEntityId,
                $storeId,
                0
            );
            if (!$webShopPrice) {
                $webShopPrice = $price;
            } elseif ($price > 0) {
                if ($webShopPrice > $price) {
                    $webShopPrice = $price;
                } else {
                    $price = $webShopPrice;
                }
            } else {
                $price = $webShopPrice;
            }

            $displayPrice = (float)$this->getDisplayPrice($price, $storeObject);

            $originalPrice = (float)$this->getStoreData(
                $productId,
                $storeId,
                'price'
            );
            $originalWebShopPrice = (float)$this->getOriginalWebShopPrice(
                $productEntityId,
                $storeId,
                0
            );
            if (!$originalWebShopPrice) {
                $originalWebShopPrice = $originalPrice;
            } elseif ($originalPrice > 0) {
                if ($originalWebShopPrice > $originalPrice) {
                    $originalWebShopPrice = $originalPrice;
                } else {
                    $originalPrice = $originalWebShopPrice;
                }
            } else {
                $originalPrice = $originalWebShopPrice;
            }

            $originalDisplayPrice = (float)$this->getDisplayPrice(
                $originalPrice,
                $storeObject
            );

            $displayWebShopPrice = (float)$this->getDisplayPrice(
                $webShopPrice,
                $storeObject
            );

            $originalDisplayWebShopPrice = (float)$this->getDisplayPrice(
                $originalWebShopPrice,
                $storeObject
            );

            if (!$this->productEnableInWebsite($productEntityId, $storeObject->getWebsiteId())) {
                $status = 0;
            } else {
                $status = $this->getStoreData($productId, $storeId, 'status');
            }

            /** @var ProductStoreDataInterface $productStoreData */
            $productStoreData =
                $this->productStoreDataFactory
                    ->create()
                    ->setStoreId($storeId)
                    ->setStatus($status)
                    ->setDescription(
                        $this->getStoreData($productId, $storeId, 'description')
                    )
                    ->setLink($this->handleLink($storeObject, $productId))
                    ->setName($this->getStoreData($productId, $storeId, 'name'))
                    ->setPrice($price)
                    ->setDisplayPrice($displayPrice)
                    ->setOriginalPrice($originalPrice)
                    ->setOriginalDisplayPrice($originalDisplayPrice)
                    ->setWebshopPrice($webShopPrice)
                    ->setDisplayWebshopPrice($displayWebShopPrice)
                    ->setOriginalWebshopPrice($originalWebShopPrice)
                    ->setOriginalDisplayWebshopPrice(
                        $originalDisplayWebShopPrice
                    )
                    ->setCurrencyCode($this->getCurrencyCode($storeObject))
                    ->setImages($this->handleImages($storeObject, $productId));

            if ($this->getProductExtraFields()) {
                $extraFields = [];
                foreach ($this->getProductExtraFields() as $field) {
                    $value = $this->getStoreData($productId, $storeId, $field);
                    $textValue = $this->getStoreAttributeValue(
                        $storeId,
                        $field,
                        $value
                    );
                    $extraField =
                        $this->extraFieldsFactory
                            ->create()
                            ->setKey($field)
                            ->setValue($value)
                            ->setTextValue($textValue);

                    if ($toArray) {
                        $extraField = $extraField->getData();
                    }

                    $extraFields[] = $extraField;
                }
                $productStoreData->setExtraFields($extraFields);
            }

            $returnArray[] = $productStoreData;
        }

        return $returnArray;
    }

    /**
     * @param int $productId
     * @param int $websiteId
     * @return bool
     */
    protected function productEnableInWebsite($productId, $websiteId): bool
    {
        // 0 store handle
        if ($websiteId == 0) {
            return true;
        }
        if (isset($this->statusData[$productId]) && is_array($this->statusData[$productId])) {
            return in_array($websiteId, $this->statusData[$productId]);
        }

        return false;
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return float
     */
    protected function getPrice($productId, $storeId)
    {
        $price = $this->getStoreData($productId, $storeId, 'price');
        $specialPrice = $this->getStoreData(
            $productId,
            $storeId,
            'special_price'
        );
        if (null !== $specialPrice) {
            try {
                $specialFromDate = $this->getStoreData(
                    $productId,
                    $storeId,
                    'special_from_date'
                );
                $specialFromDate = new DateTime($specialFromDate);

                $now = new DateTime();

                $specialToDate = $this->getStoreData(
                    $productId,
                    $storeId,
                    'special_to_date'
                );
                $specialToDate = new DateTime($specialToDate);

                if ($specialFromDate <= $now && $now <= $specialToDate) {
                    $price = $specialPrice;
                }
            } catch (Exception $e) {
                $specialPrice = null;
            }
        }

        return (float)$price;
    }

    /**
     * @param int $productEntityId
     * @param int $storeId
     * @param int $customerGroupId
     *
     * @return float
     */
    protected function getWebShopPrice(
        $productEntityId,
        $storeId,
        $customerGroupId = 0
    ) {
        if (array_key_exists($productEntityId, $this->priceData)
            && array_key_exists($storeId, $this->priceData[$productEntityId])
            && array_key_exists(
                $customerGroupId,
                $this->priceData[$productEntityId][$storeId]
            )
            && array_key_exists(
                'final_price',
                $this->priceData[$productEntityId][$storeId][$customerGroupId]
            )
        ) {
            return $this->priceData[$productEntityId][$storeId][$customerGroupId]['final_price'];
        }

        return 0;
    }

    /**
     * @param int $productEntityId
     * @param int $storeId
     * @param int $customerGroupId
     *
     * @return float
     */
    protected function getOriginalWebShopPrice(
        $productEntityId,
        $storeId,
        $customerGroupId = 0
    ) {
        if (array_key_exists($productEntityId, $this->priceData)
            && array_key_exists($storeId, $this->priceData[$productEntityId])
            && array_key_exists(
                $customerGroupId,
                $this->priceData[$productEntityId][$storeId]
            )
            && array_key_exists(
                'price',
                $this->priceData[$productEntityId][$storeId][$customerGroupId]
            )
        ) {
            return $this->priceData[$productEntityId][$storeId][$customerGroupId]['price'];
        }

        return 0;
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
                $tmp = $store->getBaseCurrency()->convert(
                    $price,
                    $store->getCurrentCurrencyCode()
                );
                $price = $tmp;
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
            }
        }

        return $price;
    }

    /**
     * @param Store $store
     * @param int   $productId
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function handleLink($store, $productId)
    {
        $link = $this->getStoreData($productId, $store->getId(), 'url_key');

        if ($link) {
            return $store->getBaseUrl() . $link . $this->getProductUrlSuffix($store->getId());
        }

        return '';
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
     * @param array      $websiteIds
     * @param int[]      $customerGroupIds
     * @param array      $wheres
     * @param array|null $joinInner
     *
     * @return $this
     */
    public function getPrices(
        $websiteIds,
        $customerGroupIds,
        $wheres,
        $joinInner = null
    ) {
        $this->priceData = $this->productResource->getPrices(
            $websiteIds,
            $customerGroupIds,
            $wheres,
            $joinInner
        );

        return $this;
    }
}
