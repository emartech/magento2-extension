<?php
/**
 * Copyright ©2018 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\ResourceModel\Api;

use Magento\Catalog\Model\Indexer\Category\Product\Processor;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResourceModel;
use Magento\Catalog\Model\ResourceModel\Category\TreeFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Eav\Model\Entity\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Factory;
use Magento\Framework\Event\ManagerInterface;
use Magento\Catalog\Model\Category as CategoryModel;

/**
 * Class Category
 *
 * @package Emartech\Emarsys\Model\ResourceModel\Api
 */
class Category extends CategoryResourceModel
{
    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * @var array
     */
    private $categoryIds = [];

    /**
     * @var array
     */
    private $categories = [];

    /**
     * Category constructor.
     *
     * @param Context                   $context
     * @param StoreManagerInterface     $storeManager
     * @param Factory                   $modelFactory
     * @param ManagerInterface          $eventManager
     * @param TreeFactory               $categoryTreeFactory
     * @param CollectionFactory         $categoryCollectionFactory
     * @param Iterator                  $iterator
     * @param Processor                 $processor
     * @param ProductMetadataInterface  $productMetadata
     * @param array                     $data
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Factory $modelFactory,
        ManagerInterface $eventManager,
        TreeFactory $categoryTreeFactory,
        CollectionFactory $categoryCollectionFactory,
        Iterator $iterator,
        Processor $processor,
        ProductMetadataInterface $productMetadata,
        array $data = []
    ) {
        $this->iterator = $iterator;

        if (version_compare($productMetadata->getVersion(), '2.3.0', '>=')) {
            parent::__construct(
                $context,
                $storeManager,
                $modelFactory,
                $eventManager,
                $categoryTreeFactory,
                $categoryCollectionFactory,
                $processor,
                $data
            );
        } else {
            parent::__construct(
                $context,
                $storeManager,
                $modelFactory,
                $eventManager,
                $categoryTreeFactory,
                $categoryCollectionFactory,
                $data
            );
        }
    }

    /**
     * @param int $minProductId
     * @param int $maxProductId
     *
     * @return array
     */
    public function getCategoryIds($minProductId, $maxProductId)
    {
        $this->categoryIds = [];

        $categoryQuery = $this->_resource->getConnection()->select()
            ->from($this->getCategoryProductTable(), ['category_id', 'product_id'])
            ->where('product_id >= ?', $minProductId)
            ->where('product_id <= ?', $maxProductId);

        $this->iterator->walk(
            (string)$categoryQuery,
            [[$this, 'handleCategoryId']],
            [],
            $this->_resource->getConnection()
        );

        return $this->categoryIds;
    }

    /**
     * @param array $args
     *
     * @return void
     */
    public function handleCategoryId($args)
    {
        $productId = $args['row']['product_id'];
        $categoryId = $args['row']['category_id'];

        if (!array_key_exists($productId, $this->categoryIds)) {
            $this->categoryIds[$productId] = [];
        }
        $this->categoryIds[$productId][] = $this->handleCategory($categoryId);
    }

    /**
     * @param int $categoryId
     *
     * @return string
     */
    private function handleCategory($categoryId)
    {
        $categoryData = $this->getCategory($categoryId);

        if ($categoryData instanceof CategoryModel) {
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
            $categoryCollection = $this->_categoryCollectionFactory->create();
            foreach ($categoryCollection as $category) {
                $this->categories[$category->getId()] = $category;
            }
        }

        return $this->categories[$categoryId];
    }
}
