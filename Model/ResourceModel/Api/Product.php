<?php
/**
 * Copyright ©2018 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\ResourceModel\Api;

use Emartech\Emarsys\Helper\LinkField;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Factory;
use Magento\Catalog\Model\Indexer\Product\Price\PriceTableResolver;
use Magento\Catalog\Model\Product\Attribute\DefaultAttributes;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResourceModel;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as ProductAttribute;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as ProductAttributeCollection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as ProductAttributeCollectionFactory;
use Magento\Customer\Model\Indexer\CustomerGroupDimensionProvider;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\Entity\Context;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Indexer\Dimension;
use Magento\Framework\Indexer\DimensionFactory;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Store\Model\Indexer\WebsiteDimensionProvider;
use Magento\Store\Model\StoreManagerInterface;
use Zend_Db_Select;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Emartech\Emarsys\Helper\DataSource as DataSourceHelper;

class Product extends ProductResourceModel
{
    const PRODUCT_ENTITY_TYPE_ID = 4;

    /**
     * @var Iterator
     */
    private $iterator;

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
    private $attributeData = [];

    /**
     * @var ProductAttributeCollectionFactory
     */
    private $productAttributeCollectionFactory;

    /**
     * @var LinkField
     */
    private $linkFieldHelper;

    /**
     * @var PriceTableResolver
     */
    private $priceTableResolver;

    /**
     * @var DimensionFactory
     */
    private $dimensionFactory;

    /**
     * @var DataSourceHelper
     */
    private $dataSourceHelper;

    /**
     * @var array
     */
    private $priceData = [];

    /**
     * @var string
     */
    private $mainTable = '';

    /**
     * @var string
     */
    private $linkField = 'entity_id';

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
     * Product constructor.
     *
     * @param Context                           $context
     * @param StoreManagerInterface             $storeManager
     * @param Factory                           $modelFactory
     * @param CollectionFactory                 $categoryCollectionFactory
     * @param CategoryResourceModel             $catalogCategory
     * @param ManagerInterface                  $eventManager
     * @param SetFactory                        $setFactory
     * @param TypeFactory                       $typeFactory
     * @param DefaultAttributes                 $defaultAttributes
     * @param ProductAttributeCollectionFactory $productAttributeCollectionFactory
     * @param Iterator                          $iterator
     * @param LinkField                         $linkFieldHelper
     * @param DataSourceHelper                  $dataSourceHelper
     * @param array                             $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Factory $modelFactory,
        CollectionFactory $categoryCollectionFactory,
        CategoryResourceModel $catalogCategory,
        ManagerInterface $eventManager,
        SetFactory $setFactory,
        TypeFactory $typeFactory,
        DefaultAttributes $defaultAttributes,
        ProductAttributeCollectionFactory $productAttributeCollectionFactory,
        Iterator $iterator,
        LinkField $linkFieldHelper,
        DataSourceHelper $dataSourceHelper,
        array $data = []
    ) {
        $this->iterator = $iterator;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->linkFieldHelper = $linkFieldHelper;
        $this->linkField = $this->linkFieldHelper->getEntityLinkField(
            ProductInterface::class
        );

        $this->dataSourceHelper = $dataSourceHelper;

        if (class_exists(PriceTableResolver::class)) {
            $this->priceTableResolver = ObjectManager::getInstance()->get(
                PriceTableResolver::class
            );
        }
        if (class_exists(Dimension::class)) {
            $this->dimensionFactory = ObjectManager::getInstance()->get(
                DimensionFactory::class
            );
        }

        parent::__construct(
            $context,
            $storeManager,
            $modelFactory,
            $categoryCollectionFactory,
            $catalogCategory,
            $eventManager,
            $setFactory,
            $typeFactory,
            $defaultAttributes,
            $data
        );
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
        if (null === $table) {
            $table = $this->getTable('catalog_product_entity');
        }
        if (null === $primaryKey) {
            $primaryKey = $this->linkField;
        }
        if (null === $countField) {
            $countField = $primaryKey;
        }

        $itemsCountQuery = $this->_resource
            ->getConnection()
            ->select()
            ->from(
                $table,
                ['count' => 'count(distinct ' . $countField . ')']
            );

        if ($wheres) {
            foreach ($wheres as $where) {
                $itemsCountQuery->where($where[0], $where[1]);
            }
        }

        $numberOfItems = $this->_resource->getConnection()->fetchOne(
            $itemsCountQuery
        );

        $subFields['eid'] = $primaryKey;

        $subSelect = $this->_resource->getConnection()->select()
                                     ->from($table, $subFields)
                                     ->order($primaryKey)
                                     ->limit($pageSize, $page);

        if ($wheres) {
            foreach ($wheres as $where) {
                $subSelect->where($where[0], $where[1]);
            }
        }

        $fields = ['minId' => 'min(tmp.eid)', 'maxId' => 'max(tmp.eid)'];

        $idQuery = $this->_resource
            ->getConnection()
            ->select()
            ->from(['tmp' => $subSelect], $fields);

        $minMaxValues = $this->_resource->getConnection()->fetchRow($idQuery);

        return [
            'numberOfItems' => (int)$numberOfItems,
            'minId'         => (int)$minMaxValues['minId'],
            'maxId'         => (int)$minMaxValues['maxId'],
        ];
    }

    /**
     * @param array      $wheres
     * @param array|null $joinInner
     *
     * @return array
     */
    public function getChildrenProductIds($wheres, $joinInner = null)
    {
        $this->childrenProductIds = [];

        $superLinkTable = $this->getTable('catalog_product_super_link');

        $superLinkQuery = $this->_resource->getConnection()->select()
                                          ->from(
                                              $superLinkTable,
                                              ['product_id', 'parent_id']
                                          );

        foreach ($wheres as $where) {
            $superLinkQuery->where($where[0], $where[1]);
        }

        if (null != $joinInner) {
            $superLinkQuery->joinInner(
                $joinInner[0],
                $joinInner[1],
                $joinInner[2]
            );
        }

        $this->iterator->walk(
            (string)$superLinkQuery,
            [[$this, 'handleChildrenProductId']],
            [],
            $this->_resource->getConnection()
        );

        return $this->childrenProductIds;
    }

    /**
     * @param array $args
     *
     * @return void
     */
    public function handleChildrenProductId($args)
    {
        $productId = $args['row']['product_id'];
        $parentId = $args['row']['parent_id'];
        if (!array_key_exists($parentId, $this->childrenProductIds)) {
            $this->childrenProductIds[$parentId] = [];
        }
        $this->childrenProductIds[$parentId][] = $productId;
    }

    /**
     * @param array      $wheres
     * @param array|null $joinInner
     *
     * @return array
     */
    public function getStockData($wheres, $joinInner = null)
    {
        $this->stockData = [];
        $stockItemTable = $this->getTable('cataloginventory_stock_item');
        $stockQuery = $this->_resource->getConnection()->select()
                                      ->from(
                                          $stockItemTable,
                                          [
                                              'is_in_stock', 'qty',
                                              'product_id',
                                          ]
                                      )
                                      ->joinLeft(
                                          [
                                              'entity_table' => $this->getTable(
                                                  'catalog_product_entity'
                                              ),
                                          ],
                                          'entity_table.entity_id = ' . $stockItemTable . '.product_id',
                                          []
                                      )->where('stock_id = ?', 1);

        foreach ($wheres as $where) {
            $stockQuery->where($where[0], $where[1]);
        }

        if (null !== $joinInner) {
            $stockQuery->joinInner($joinInner[0], $joinInner[1], $joinInner[2]);
        }

        $this->iterator->walk(
            (string)$stockQuery,
            [[$this, 'handleStockItem']],
            [],
            $this->_resource->getConnection()
        );

        return $this->stockData;
    }

    /**
     * @param array $args
     *
     * @return void
     */
    public function handleStockItem($args)
    {
        $productId = $args['row']['product_id'];
        $isInStock = $args['row']['is_in_stock'];
        $qty = $args['row']['qty'];

        $this->stockData[$productId] = [
            'is_in_stock' => $isInStock,
            'qty'         => $qty,
        ];
    }

    /**
     * @param array      $wheres
     * @param array|null $joinInner
     * @return array
     */
    public function getStatusData($wheres, $joinInner = null)
    {
        $this->statusData = [];
        $productWebsiteTable = $this->getTable('catalog_product_website');
        $productWebsiteQuery = $this->_resource->getConnection()->select()
            ->from(
                $productWebsiteTable,
                [
                    'product_id',
                    'website_id'
                ]
            )
            ->joinLeft(
                [
                    'entity_table' => $this->getTable(
                        'catalog_product_entity'
                    ),
                ],
                'entity_table.entity_id = ' . $productWebsiteTable . '.product_id',
                []
            );

        foreach ($wheres as $where) {
            $productWebsiteQuery->where($where[0], $where[1]);
        }

        if (null !== $joinInner) {
            $productWebsiteQuery->joinInner($joinInner[0], $joinInner[1], $joinInner[2]);
        }

        $this->iterator->walk(
            (string)$productWebsiteQuery,
            [[$this, 'handleStatusItem']],
            [],
            $this->_resource->getConnection()
        );

        return $this->statusData;
    }

    /**
     * @param array $args
     *
     * @return void
     */
    public function handleStatusItem($args)
    {
        $productId = $args['row']['product_id'];
        $websiteId = $args['row']['website_id'];

        if (!isset($this->statusData[$productId])) {
            $this->statusData[$productId] = [];
        }

        $this->statusData[$productId][] = (int)$websiteId;
    }

    /**
     * @param array      $wheres
     * @param array      $storeIds
     * @param string[]   $attributeCodes
     * @param array|null $joinInner
     *
     * @return array
     */
    public function getAttributeData(
        $wheres,
        $storeIds,
        $attributeCodes,
        $joinInner = null
    ) {
        $this->mainTable = $this->getEntityTable();
        $this->attributeData = [];

        $attributeMapper = [];
        $mainTableFields = [];
        $attributeTables = [];
        $sourceModels = [];

        /** @var ProductAttributeCollection $productAttributeCollection */
        $productAttributeCollection =
            $this->productAttributeCollectionFactory->create();
        $productAttributeCollection
            ->addFieldToFilter(
                'entity_type_id',
                ['eq' => self::PRODUCT_ENTITY_TYPE_ID]
            )
            ->addFieldToFilter('attribute_code', ['in' => $attributeCodes]);

        /** @var ProductAttribute $productAttribute */
        foreach ($productAttributeCollection as $productAttribute) {
            if ($sourceModel = $productAttribute->getSourceModel()) {
                try {
                    $sourceModels[$productAttribute->getAttributeCode()] =
                        $productAttribute->getSource();
                } catch (\Exception $e) { } // @codingStandardsIgnoreLine
            }

            $attributeTable = $productAttribute->getBackendTable();
            if ($this->mainTable === $attributeTable) {
                $mainTableFields[] = $productAttribute->getAttributeCode();
            } else {
                if (!in_array($attributeTable, $attributeTables)) {
                    $attributeTables[] = $attributeTable;
                }
                $attributeMapper[$productAttribute->getAttributeCode()] =
                    (int)$productAttribute->getId();
            }
        }

        $this
            ->getMainTableFieldItems(
                $mainTableFields,
                $wheres,
                $storeIds,
                $attributeMapper,
                $joinInner
            )->getAttributeTableFieldItems(
                $attributeTables,
                $wheres,
                $storeIds,
                $attributeMapper,
                $joinInner
            );

        $attributeValues = $this->dataSourceHelper->getAllOptions(
            $sourceModels,
            $storeIds
        );

        return [
            'attribute_data'   => $this->attributeData,
            'attribute_values' => $attributeValues,
        ];
    }

    /**
     * @param array      $mainTableFields
     * @param array      $wheres
     * @param array      $storeIds
     * @param array      $attributeMapper
     * @param array|null $joinInner
     *
     * @return $this
     */
    private function getMainTableFieldItems(
        $mainTableFields,
        $wheres,
        $storeIds,
        $attributeMapper,
        $joinInner = null
    ) {
        if ($mainTableFields) {
            if (!in_array($this->linkField, $mainTableFields)) {
                $mainTableFields[] = $this->linkField;
            }
            $attributesQuery = $this->_resource->getConnection()->select()
                                               ->from(
                                                   $this->mainTable,
                                                   $mainTableFields
                                               );

            foreach ($wheres as $where) {
                $attributesQuery->where($where[0], $where[1]);
            }

            if (null !== $joinInner) {
                $attributesQuery->joinInner(
                    $joinInner[0],
                    str_replace('{TABLE}', $this->mainTable, $joinInner[1]),
                    $joinInner[2]
                );
            }

            $this->iterator->walk(
                (string)$attributesQuery,
                [[$this, 'handleMainTableAttributeDataTable']],
                [
                    'storeIds'        => $storeIds,
                    'fields'          => array_diff(
                        $mainTableFields,
                        [$this->linkField]
                    ),
                    'attributeMapper' => $attributeMapper,
                ],
                $this->_resource->getConnection()
            );
        }

        return $this;
    }

    /**
     * @param array      $attributeTables
     * @param array      $wheres
     * @param array      $storeIds
     * @param array      $attributeMapper
     * @param array|null $joinInner
     *
     * @return $this
     */
    private function getAttributeTableFieldItems(
        $attributeTables,
        $wheres,
        $storeIds,
        $attributeMapper,
        $joinInner = null
    ) {
        $attributeQueries = [];

        foreach ($attributeTables as $attributeTable) {
            $attributeQuery = $this->_resource->getConnection()->select()
                                              ->from(
                                                  $attributeTable,
                                                  [
                                                      'attribute_id',
                                                      'store_id',
                                                      $this->linkField, 'value',
                                                  ]
                                              )
                                              ->where(
                                                  'store_id IN (?)',
                                                  $storeIds
                                              )
                                              ->where(
                                                  'attribute_id IN (?)',
                                                  $attributeMapper
                                              );

            foreach ($wheres as $where) {
                $attributeQuery->where($where[0], $where[1]);
            }

            if (null !== $joinInner) {
                $attributeQuery->joinInner(
                    $joinInner[0],
                    str_replace('{TABLE}', $attributeTable, $joinInner[1]),
                    $joinInner[2]
                );
            }

            $attributeQueries[] = $attributeQuery;
        }

        try {
            $unionQuery = $this->_resource->getConnection()->select()
                                          ->union(
                                              $attributeQueries,
                                              Zend_Db_Select::SQL_UNION_ALL
                                          ); // @codingStandardsIgnoreLine

            $this->iterator->walk(
                (string)$unionQuery,
                [[$this, 'handleAttributeDataTable']],
                [
                    'attributeMapper' => $attributeMapper,
                ],
                $this->_resource->getConnection()
            );
        } catch (\Exception $e) { // @codingStandardsIgnoreLine
        }

        return $this;
    }

    /**
     * @param array $args
     *
     * @return void
     */
    public function handleMainTableAttributeDataTable($args)
    {
        $productId = $args['row'][$this->linkField];

        foreach ($args['storeIds'] as $storeId) {
            $this->initStoreProductData($productId, $storeId);

            foreach ($args['fields'] as $field) {
                $this->attributeData[$productId][$storeId][$field] = $args['row'][$field];
            }
        }
    }

    /**
     * @param array $args
     *
     * @return void
     */
    public function handleAttributeDataTable($args)
    {
        $productId = $args['row'][$this->linkField];
        $attributeCode = $this->findAttributeCodeById(
            $args['row']['attribute_id'],
            $args['attributeMapper']
        );
        $storeId = $args['row']['store_id'];

        $this->initStoreProductData($productId, $storeId);

        $this->attributeData[$productId][$storeId][$attributeCode] = $args['row']['value'];
    }

    /**
     * @param int   $attributeId
     * @param array $attributeMapper
     *
     * @return string
     */
    private function findAttributeCodeById($attributeId, $attributeMapper)
    {
        foreach ($attributeMapper as $attributeCode => $attributeCodeId) {
            if ($attributeId == $attributeCodeId) {
                return $attributeCode;
            }
        }

        return '';
    }

    /**
     * @param int $productId
     * @param int $storeId
     *
     * @return void
     */
    private function initStoreProductData($productId, $storeId)
    {
        if (!array_key_exists($productId, $this->attributeData)) {
            $this->attributeData[$productId] = [];
        }

        if (!array_key_exists($storeId, $this->attributeData[$productId])) {
            $this->attributeData[$productId][$storeId] = [];
        }
    }

    /**
     * @param array $websiteIds
     * @param int[] $customerGroupIds
     * @param array $wheres
     * @param array $joinInner
     *
     * @return array
     */
    public function getPrices(
        $websiteIds,
        $customerGroupIds,
        $wheres,
        $joinInner
    ) {
        $this->priceData = [];

        if (empty($websiteIds)) {
            return $this->priceData;
        }

        $columns = [
            'entity_id',
            'website_id',
            'customer_group_id',
            'price',
            'tax_class_id',
            'final_price',
            'minimal_price' => $this->_resource->getConnection()->getCheckSql(
                'tier_price IS NOT NULL',
                $this->_resource->getConnection()->getLeastSql(
                    ['min_price', 'tier_price']
                ),
                'min_price'
            ),
            'min_price',
            'max_price',
            'tier_price',
        ];

        $tables = [];
        foreach ($websiteIds as $websiteId => $storeIds) {
            foreach ($customerGroupIds as $customerGroupId) {
                $table = $this->getPriceIndexTable(
                    $websiteId,
                    $customerGroupId
                );
                $tables[$table] = $table;
            }
        }

        $unionSelects = [];
        foreach ($tables as $table) {
            $select = $this->_resource->getConnection()->select()->reset()
                ->from($table, $columns)
                ->joinLeft(
                    [
                        'entity_table' => $this->getTable(
                            'catalog_product_entity'
                        ),
                    ],
                    'entity_table.entity_id = ' . $table . '.entity_id',
                    []
                )
                ->where(
                    'website_id IN (?)',
                    array_keys($websiteIds)
                )
                ->where(
                    'customer_group_id IN (?)',
                    $customerGroupIds
                );

            foreach ($wheres as $where) {
                $select->where($where[0], $where[1]);
            }

            if (null !== $joinInner) {
                $select->joinInner(
                    $joinInner[0],
                    str_replace('{TABLE}', $table, $joinInner[1]),
                    $joinInner[2]
                );
            }

            $unionSelects[] = $select;
        }

        $unionQuery = $this->_resource->getConnection()->select()
            ->union(
                $unionSelects,
                Zend_Db_Select::SQL_UNION_ALL  // @codingStandardsIgnoreLine
            );

        $this->iterator->walk(
            (string)$unionQuery,
            [[$this, 'handleProductPriceTable']],
            [
                'websiteIds' => $websiteIds,
            ],
            $this->_resource->getConnection()
        );

        return $this->priceData;
    }

    /**
     * @param array $args
     *
     * @return void
     */
    public function handleProductPriceTable($args)
    {
        $websiteId = $args['row']['website_id'];

        if (array_key_exists($websiteId, $args['websiteIds'])) {
            foreach ($args['websiteIds'][$websiteId] as $storeId) {
                $entityId = $args['row']['entity_id'];
                $customerGroupId = $args['row']['customer_group_id'];

                if (!array_key_exists($entityId, $this->priceData)) {
                    $this->priceData[$entityId] = [];
                }
                if (!array_key_exists($storeId, $this->priceData[$entityId])) {
                    $this->priceData[$entityId][$storeId] = [];
                }

                $minPrice = (float)$args['row']['minimal_price'];
                $price = (float)$args['row']['price'];
                if (!$price) {
                    $price = $minPrice;
                }
                $finalPrice = (float)$args['row']['final_price'];
                if (!$finalPrice) {
                    $finalPrice = $minPrice;
                }

                $this->priceData[$entityId][$storeId][$customerGroupId] = [
                    'price'         => $price,
                    'final_price'   => $finalPrice,
                    'minimal_price' => $minPrice,
                ];
            }
        }
    }

    /**
     * @param int $websiteId
     * @param int $customerGroupId
     *
     * @return string
     */
    private function getPriceIndexTable($websiteId, $customerGroupId)
    {

        if (!($this->priceTableResolver instanceof PriceTableResolver)) {
            return $this->_resource->getTableName(
                'catalog_product_index_price'
            );
        }

        return $this->priceTableResolver->resolve(
            'catalog_product_index_price',
            [
                $this->dimensionFactory->create(
                    WebsiteDimensionProvider::DIMENSION_NAME,
                    (string)$websiteId
                ),
                $this->dimensionFactory->create(
                    CustomerGroupDimensionProvider::DIMENSION_NAME,
                    (string)$customerGroupId
                ),
            ]
        );
    }
}
