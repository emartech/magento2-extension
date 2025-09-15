<?php
/**
 * Copyright ©2018 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\CategoriesApiInterface;
use Emartech\Emarsys\Api\Data\CategoriesApiResponseInterface;
use Emartech\Emarsys\Api\Data\CategoriesApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\CategoryInterfaceFactory;
use Emartech\Emarsys\Api\Data\CategoryStoreDataInterfaceFactory;
use Emartech\Emarsys\Helper\LinkField;
use Exception;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection as CategoryAttributeCollection;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory as CategoryAttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class CategoriesApi implements CategoriesApiInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var CategoryCollection
     */
    private $categoryCollection;

    /**
     * @var CategoryAttributeCollectionFactory
     */
    private $categoryAttributeCollectionFactory;

    /**
     * @var CategoryAttributeCollection
     */
    private $categoryAttributeCollection;

    /**
     * @var CategoriesApiResponseInterfaceFactory
     */
    private $categoriesApiResponseFactory;

    /**
     * @var CategoryInterfaceFactory
     */
    private $categoryFactory;

    /**
     * @var CategoryStoreDataInterfaceFactory
     */
    private $categoryStoreDataFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var CategoryUrlPathGenerator
     */
    private $categoryUrlPathGenerator;

    /**
     * @var array
     */
    private $storeIds = [];

    /**
     * @var array
     */
    private $storeCategoryAttributeCodes = ['name', 'image', 'description', 'is_active', 'store_id'];

    /**
     * @var array
     */
    private $globalCategoryAttributeCodes = ['entity_id', 'path', 'children_count', 'stores'];

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
     * CategoriesApi constructor.
     *
     * @param StoreManagerInterface                 $storeManager
     * @param CategoryCollectionFactory             $categoryCollectionFactory
     * @param ScopeConfigInterface                  $scopeConfig
     * @param CategoryAttributeCollectionFactory    $categoryAttributeCollectionFactory
     * @param CategoriesApiResponseInterfaceFactory $categoriesApiResponseFactory
     * @param CategoryInterfaceFactory              $categoryFactory
     * @param CategoryStoreDataInterfaceFactory     $categoryStoreDataFactory
     * @param CategoryUrlPathGenerator              $categoryUrlPathGenerator
     * @param ObjectManagerInterface                $objectManager
     * @param LinkField                             $linkFieldHelper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CategoryCollectionFactory $categoryCollectionFactory,
        ScopeConfigInterface $scopeConfig,
        CategoryAttributeCollectionFactory $categoryAttributeCollectionFactory,
        CategoriesApiResponseInterfaceFactory $categoriesApiResponseFactory,
        CategoryInterfaceFactory $categoryFactory,
        CategoryStoreDataInterfaceFactory $categoryStoreDataFactory,
        CategoryUrlPathGenerator $categoryUrlPathGenerator,
        ObjectManagerInterface $objectManager,
        LinkField $linkFieldHelper
    ) {
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->categoryAttributeCollectionFactory = $categoryAttributeCollectionFactory;
        $this->categoriesApiResponseFactory = $categoriesApiResponseFactory;
        $this->categoryFactory = $categoryFactory;
        $this->categoryStoreDataFactory = $categoryStoreDataFactory;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->objectManager = $objectManager;
        $this->linkFieldHelper = $linkFieldHelper;
        $this->linkField = $this->linkFieldHelper->getEntityLinkField(CategoryInterface::class);
    }

    /**
     * Get
     *
     * @param int    $page
     * @param int    $pageSize
     * @param string $storeId
     *
     * @return CategoriesApiResponseInterface
     * @throws LocalizedException
     * @throws WebApiException
     */
    public function get(int $page, int $pageSize, string $storeId): CategoriesApiResponseInterface
    {
        $this
            ->initStores($storeId);

        if (!array_key_exists(0, $this->storeIds)) {
            throw new WebApiException(__('Store ID must contain 0'));
        }

        $this
            ->initCollection()
            ->joinData()
            ->setOrder()
            ->setPage($page, $pageSize);

        return $this->categoriesApiResponseFactory
            ->create()->setCurrentPage($this->categoryCollection->getCurPage())
            ->setLastPage($this->categoryCollection->getLastPageNumber())
            ->setPageSize($this->categoryCollection->getPageSize())
            ->setTotalCount($this->categoryCollection->getSize())
            ->setCategories($this->handleCategories());
    }

    /**
     * InitStores
     *
     * @param int|int[] $storeIds
     *
     * @return CategoriesApi
     */
    private function initStores($storeIds): CategoriesApi
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
     * InitCollection
     *
     * @return CategoriesApi
     * @throws Exception
     */
    private function initCollection(): CategoriesApi
    {
        $this->categoryCollection = $this->categoryCollectionFactory->create();

        return $this;
    }

    /**
     * JoinData
     *
     * @return CategoriesApi
     * @throws LocalizedException
     */
    private function joinData(): CategoriesApi
    {
        $this->categoryAttributeCollection = $this->categoryAttributeCollectionFactory
            ->create()
            ->addFieldToFilter('attribute_code', [
                'in' => array_values(
                    array_merge(
                        $this->storeCategoryAttributeCodes,
                        $this->globalCategoryAttributeCodes
                    )
                ),
            ]);

        $mainTableName = $this->categoryCollection->getResource()->getTable('catalog_category_entity');

        foreach ($this->categoryAttributeCollection as $categoryAttribute) {
            if ($categoryAttribute->getBackendTable() === $mainTableName) {
                $this->categoryCollection->addAttributeToSelect($categoryAttribute->getAttributeCode());
            } elseif (in_array($categoryAttribute->getAttributeCode(), $this->globalCategoryAttributeCodes)) {
                $valueAlias = $this->getAttributeValueAlias($categoryAttribute->getAttributeCode());

                $this->categoryCollection->joinAttribute(
                    $valueAlias,
                    'catalog_category/' . $categoryAttribute->getAttributeCode(),
                    $this->linkField,
                    null,
                    'left'
                );
            } else {
                foreach (array_keys($this->storeIds) as $storeId) {
                    $valueAlias = $this->getAttributeValueAlias($categoryAttribute->getAttributeCode(), $storeId);

                    $this->categoryCollection->joinAttribute(
                        $valueAlias,
                        'catalog_category/' . $categoryAttribute->getAttributeCode(),
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
     * GetAttributeValueAlias
     *
     * @param string   $attributeCode
     * @param int|null $storeId
     *
     * @return string
     */
    private function getAttributeValueAlias(string $attributeCode, ?int $storeId = null): string
    {
        $returnValue = $attributeCode;
        if ($storeId !== null) {
            $returnValue .= '_' . $storeId;
        }

        return $returnValue;
    }

    /**
     * SetOrder
     *
     * @return $this
     */
    private function setOrder(): CategoriesApi
    {
        $this->categoryCollection->setOrder($this->linkField, DataCollection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * SetPage
     *
     * @param int $page
     * @param int $pageSize
     *
     * @return CategoriesApi
     */
    private function setPage(int $page, int $pageSize): CategoriesApi
    {
        $this->categoryCollection->setPage($page, $pageSize);

        return $this;
    }

    /**
     * HandleCategories
     *
     * @return array
     */
    private function handleCategories(): array
    {
        $returnArray = [];

        foreach ($this->categoryCollection as $category) {
            $returnArray[] = $this->categoryFactory
                ->create()
                ->setPath($category->getPath())
                ->setEntityId($category->getId())
                ->setChildrenCount($category->getChildrenCount())
                ->setStoreData($this->handleCategoryStoreData($category));
        }

        return $returnArray;
    }

    /**
     * HandleCategoryStoreData
     *
     * @param Category $category
     *
     * @return array
     */
    private function handleCategoryStoreData(Category $category): array
    {
        $returnArray = [];

        foreach ($this->storeIds as $storeId => $storeObject) {
            $returnArray[] = $this->categoryStoreDataFactory
                ->create()
                ->setStoreId($storeId)
                ->setIsActive($category->getData($this->getAttributeValueAlias('is_active', $storeId)) ?? 0)
                ->setImage($this->handleImage($category, $storeObject))
                ->setName($category->getData($this->getAttributeValueAlias('name', $storeId)))
                ->setDescription(
                    $category->getData($this->getAttributeValueAlias('description', $storeId)) ?? ''
                );
        }

        return $returnArray;
    }

    /**
     * HandleImage
     *
     * @param Category $category
     * @param Store    $store
     *
     * @return string
     */
    private function handleImage(Category $category, Store $store): string
    {
        $imagePreUrl = $this->storeIds[0]->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/category/';
        $image = $category->getData($this->getAttributeValueAlias('image', $store->getId()));

        if ($image) {
            return $imagePreUrl . $image;
        }

        return '';
    }
}
