<?php
/**
 * Copyright ©2018 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\ResourceModel\Api;

use Magento\Catalog\Model\ResourceModel\Category as CategoryResourceModel;
use Magento\Catalog\Model\ResourceModel\Product as ProductResourceModel;
use Magento\Eav\Model\Entity\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Factory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Catalog\Model\Product\Attribute\DefaultAttributes;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as ProductAttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as ProductAttributeCollection;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as ProductAttribute;

/**
 * Class Product
 *
 * @package Emartech\Emarsys\Model\ResourceModel\Api
 */
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
    private $attributeData = [];

    /**
     * @var ProductAttributeCollectionFactory
     */
    private $productAttributeCollectionFactory;

    private $mainTable = '';

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
        'currency'
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
     * @var string
     */
    protected $linkedField = 'entity_id';

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
        array $data = []
    ) {
        $this->iterator = $iterator;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;

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
     * @param null $linkedField
     * @return Product
     */
    public function setLinkedField($linkedField = null)
    {
        if ($linkedField) {
            $this->linkedField = $linkedField;
        }

        return $this;
    }

    /**
     * @param $page
     * @param $pageSize
     *
     * @return array
     * @throws \Zend_Db_Statement_Exception
     */
    public function handleIds($page, $pageSize)
    {
        $productsTable = $this->getTable('catalog_product_entity');

        $itemsCountQuery = $this->_resource->getConnection()->select()
            ->from($productsTable, ['count' => 'count(' . $this->linkedField . ')']);

        $numberOfItems = $this->_resource->getConnection()->fetchOne($itemsCountQuery);

        $subFields = ['eid' => $this->linkedField];

        $subSelect = $this->_resource->getConnection()->select()
            ->from($productsTable, $subFields)
            ->order($this->linkedField)
            ->limit($pageSize, $page);

        $fields = ['minId' => 'min(tmp.eid)', 'maxId' => 'max(tmp.eid)'];

        $idQuery = $this->_resource->getConnection()->select()
            ->from(['tmp' => $subSelect], $fields);

        $minMaxValues = $this->_resource->getConnection()->fetchRow($idQuery);

        $returnArray = [
            'numberOfItems' => (int)$numberOfItems,
            'minId'         => (int)$minMaxValues['minId'],
            'maxId'         => (int)$minMaxValues['maxId'],
        ];

        return $returnArray;
    }

    /**
     * @param int    $minProductId
     * @param int    $maxProductId
     *
     * @return array
     */
    public function getChildrenProductIds($minProductId, $maxProductId)
    {
        $this->childrenProductIds = [];

        $superLinkTable = $this->getTable('catalog_product_super_link');

        $superLinkQuery = $this->_resource->getConnection()->select()
            ->from($superLinkTable, ['product_id', 'parent_id'])
            ->where('parent_id >= ?', $minProductId)
            ->where('parent_id <= ?', $maxProductId);

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
     * @param int $minProductId
     * @param int $maxProductId
     *
     * @return array
     */
    public function getStockData($minProductId, $maxProductId)
    {
        $this->stockData = [];
        $stockQuery = $this->_resource->getConnection()->select()
            ->from($this->getTable('cataloginventory_stock_item'), ['is_in_stock', 'qty', 'product_id'])
            ->where('product_id >= ?', $minProductId)
            ->where('product_id <= ?', $maxProductId)
            ->where('stock_id = ?', 1);

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

    public function getAttributeData($minProductId, $maxProductId, $storeIds)
    {
        $this->mainTable = $this->getEntityTable();

        $this->attributeData = [];

        $attributeMapper = [];
        $mainTableFields = [];
        $attributeTables = [];

        /** @var ProductAttributeCollection $productAttributeCollection */
        $productAttributeCollection = $this->productAttributeCollectionFactory->create();
        $productAttributeCollection
            ->addFieldToFilter('entity_type_id', ['eq' => self::PRODUCT_ENTITY_TYPE_ID])
            ->addFieldToFilter('attribute_code', [
                'in' => array_values(array_merge(
                    $this->storeProductAttributeCodes,
                    $this->globalProductAttributeCodes
                )),
            ]);

        /** @var ProductAttribute $productAttribute */
        foreach ($productAttributeCollection as $productAttribute) {
            $attributeTable = $productAttribute->getBackendTable();
            if ($this->mainTable === $attributeTable) {
                $mainTableFields[] = $productAttribute->getAttributeCode();
            } else {
                if (!in_array($attributeTable, $attributeTables)) {
                    $attributeTables[] = $attributeTable;
                }
                $attributeMapper[$productAttribute->getAttributeCode()] = (int)$productAttribute->getId();
            }
        }

        $this
            ->getMainTableFieldItems($mainTableFields, $minProductId, $maxProductId, $storeIds, $attributeMapper)
            ->getAttributeTableFieldItems($attributeTables, $minProductId, $maxProductId, $storeIds, $attributeMapper)
            ->getPrices($minProductId, $maxProductId, $storeIds);

        return $this->attributeData;
    }

    /**
     * @param array $mainTableFields
     * @param int   $minProductId
     * @param int   $maxProductId
     * @param array $storeIds
     * @param array $attributeMapper
     *
     * @return $this
     */
    private function getMainTableFieldItems($mainTableFields, $minProductId, $maxProductId, $storeIds, $attributeMapper)
    {
        if ($mainTableFields) {
            if (!in_array($this->linkedField, $mainTableFields)) {
                $mainTableFields[] = $this->linkedField;
            }
            $attributesQuery = $this->_resource->getConnection()->select()
                ->from($this->getTable($this->mainTable), $mainTableFields)
                ->where($this->linkedField . ' >= ?', $minProductId)
                ->where($this->linkedField . ' <= ?', $maxProductId);

            $this->iterator->walk(
                (string)$attributesQuery,
                [[$this, 'handleMainTableAttributeDataTable']],
                [
                    'storeIds'        => $storeIds,
                    'fields'          => array_diff($mainTableFields, [$this->linkedField]),
                    'attributeMapper' => $attributeMapper,
                ],
                $this->_resource->getConnection()
            );
        }

        return $this;
    }

    /**
     * @param array $attributeTables
     * @param int   $minProductId
     * @param int   $maxProductId
     * @param array $storeIds
     * @param array $attributeMapper
     *
     * @return $this
     */
    private function getAttributeTableFieldItems(
        $attributeTables,
        $minProductId,
        $maxProductId,
        $storeIds,
        $attributeMapper
    ) {
        $attributeQueries = [];

        foreach ($attributeTables as $attributeTable) {
            $attributeQueries[] = $this->_resource->getConnection()->select()
                ->from($this->getTable($attributeTable), ['attribute_id', 'store_id', $this->linkedField, 'value'])
                ->where($this->linkedField . ' >= ?', $minProductId)
                ->where($this->linkedField . ' <= ?', $maxProductId)
                ->where('store_id IN (?)', $storeIds)
                ->where('attribute_id IN (?)', $attributeMapper);
        }

        try {
            $unionQuery = $this->_resource->getConnection()->select()
                ->union($attributeQueries, \Zend_Db_Select::SQL_UNION_ALL); // @codingStandardsIgnoreLine
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
     * @param int   $minProductId
     * @param int   $maxProductId
     * @param array $storeIds
     *
     * @return $this
     */
    public function getPrices($minProductId, $maxProductId, $storeIds)
    {
        $websiteId = 0;
        foreach ($storeIds as $storeId) {
            if ($storeId != 0) {
                $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
                break;
            }
        }

        $connection = $this->_resource->getConnection();

        $cond = $connection->prepareSqlCondition('customer_group_id', 0)
            . ' ' . \Magento\Framework\DB\Select::SQL_AND . ' '
            . $connection->prepareSqlCondition('website_id', $websiteId);

        $least = $connection->getLeastSql(['min_price', 'tier_price']);
        $minimalExpr = $connection->getCheckSql(
            'tier_price IS NOT NULL',
            $least,
            'min_price'
        );

        $fields = [
            $this->linkedField => 'entity_id',
            'price' => $minimalExpr,
        ];

        $query = $connection->select()
            ->from($this->getTable('catalog_product_index_price'), $fields)
            ->where($cond)
            ->where( 'entity_id >= ?', $minProductId)
            ->where('entity_id <= ?', $maxProductId);

        try {
            $this->iterator->walk(
                (string)$query,
                [[$this, 'handlePriceDataTable']],
                [
                    'store_ids' => $storeIds,
                ],
                $this->_resource->getConnection()
            );
        } catch (\Exception $e) { // @codingStandardsIgnoreLine
        }

        return $this;
    }

    public function handlePriceDataTable($args)
    {
        $productId = $args['row'][$this->linkedField];

        foreach ($args['store_ids'] as $storeId) {
            $this->attributeData[$productId][$storeId]['price'] = $args['row']['price'];
        }
    }

    /**
     * @param array $args
     *
     * @return void
     */
    public function handleMainTableAttributeDataTable($args)
    {
        $productId = $args['row'][$this->linkedField];

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
        $productId = $args['row'][$this->linkedField];
        $attributeCode = $this->findAttributeCodeById($args['row']['attribute_id'], $args['attributeMapper']);
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
}
