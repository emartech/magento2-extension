<?php
/**
 * Copyright ©2018 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\ResourceModel\Api;

use Magento\Catalog\Model\ResourceModel\Category\TreeFactory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Eav\Model\Entity\Context;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Eav\Model\Entity\AbstractEntity;

class Category extends AbstractEntity
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
     * Category collection factory
     *
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * Category constructor.
     *
     * @param Context           $context
     * @param CollectionFactory $categoryCollectionFactory
     * @param Iterator          $iterator
     * @param array             $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $categoryCollectionFactory,
        Iterator $iterator,
        array $data = []
    ) {
        $this->iterator = $iterator;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param array      $wheres
     * @param array|null $joinInner
     *
     * @return array
     */
    public function getCategoryIds($wheres, $joinInner = null)
    {
        $this->categoryIds = [];

        $categoryTable = $this->getTable('catalog_category_product');

        $categoryQuery = $this->_resource->getConnection()->select()
            ->from(
                $categoryTable,
                ['category_id', 'product_id']
            )->joinLeft(
                [
                    'entity_table' => $this->getTable(
                        'catalog_product_entity'
                    ),
                ],
                'entity_table.entity_id = ' . $categoryTable . '.product_id',
                []
            );

        foreach ($wheres as $where) {
            $categoryQuery->where($where[0], $where[1]);
        }

        if (null !== $joinInner) {
            $categoryQuery->joinInner($joinInner[0], $joinInner[1], $joinInner[2]);
        }

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
            $categoryCollection = $this->categoryCollectionFactory->create();
            foreach ($categoryCollection as $category) {
                $this->categories[$category->getId()] = $category;
            }
        }

        return $this->categories[$categoryId];
    }
}
