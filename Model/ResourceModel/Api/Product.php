<?php
/**
 * Copyright ©2018 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\ResourceModel\Api;

use Magento\Catalog\Model\Indexer\Category\Product\TableMaintainer;
use Magento\Catalog\Model\ResourceModel\Category;
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
use phpDocumentor\Reflection\Element;

/**
 * Class Product
 *
 * @package Emartech\Emarsys\Model\ResourceModel\Api
 */
class Product extends ProductResourceModel
{
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
     * Product constructor.
     *
     * @param Context               $context
     * @param StoreManagerInterface $storeManager
     * @param Factory               $modelFactory
     * @param CollectionFactory     $categoryCollectionFactory
     * @param Category              $catalogCategory
     * @param ManagerInterface      $eventManager
     * @param SetFactory            $setFactory
     * @param TypeFactory           $typeFactory
     * @param DefaultAttributes     $defaultAttributes
     * @param Iterator              $iterator
     * @param array                 $data
     * @param TableMaintainer|null  $tableMaintainer
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Factory $modelFactory,
        CollectionFactory $categoryCollectionFactory,
        Category $catalogCategory,
        ManagerInterface $eventManager,
        SetFactory $setFactory,
        TypeFactory $typeFactory,
        DefaultAttributes $defaultAttributes,
        Iterator $iterator,
        array $data = [],
        TableMaintainer $tableMaintainer = null
    ) {
        $this->iterator = $iterator;

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
            $data,
            $tableMaintainer
        );
    }

    /**
     * @param $page
     * @param $pageSize
     * @param $linkField
     *
     * @return array
     * @throws \Zend_Db_Statement_Exception
     */
    public function handleIds($page, $pageSize, $linkField)
    {
        $productsTable = $this->getTable('catalog_product_entity');

        $itemsCountQuery = $this->_resource->getConnection()->select()
            ->from($productsTable, ['count' => 'count(' . $linkField . ')']);

        $numberOfItems = $this->_resource->getConnection()->fetchOne($itemsCountQuery);

        $subFields = ['eid' => 'entity_id'];
        if($linkField !== 'entity_id') {
            $subFields['eeid'] = $linkField;
        }

        $subSelect = $this->_resource->getConnection()->select()
            ->from($productsTable, $subFields)
            ->order('entity_id')
            ->limit($pageSize, $page);

        $fields = ['minId' => 'min(tmp.eid)', 'maxId' => 'max(tmp.eid)'];
        if ($linkField !== 'entity_id') {
            $fields['minEId'] = 'min(tmp.eeid)';
            $fields['maxEId'] = 'max(tmp.eeid)';
        }

        $idQuery = $this->_resource->getConnection()->select()
            ->from(['tmp' => $subSelect], $fields);

        $minMaxValues = $this->_resource->getConnection()->fetchRow($idQuery);

        $returnArray = [
            'numberOfItems' => (int)$numberOfItems,
            'minId'         => (int)$minMaxValues['minId'],
            'maxId'         => (int)$minMaxValues['maxId'],
        ];

        if (array_key_exists('minEId', $minMaxValues) && array_key_exists('maxEId', $minMaxValues)) {
            $returnArray['minEId'] = $minMaxValues['minEId'];
            $returnArray['maxEId'] = $minMaxValues['maxEId'];
        }

        return $returnArray;
    }

    /**
     * @param int    $minProductId
     * @param int    $maxProductId
     * @param string $linkField
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

        /*print($superLinkQuery);
        var_dump($this->childrenProductIds);die();*/

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
}
