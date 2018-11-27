<?php

namespace Emartech\Emarsys\Model\Api;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\CollectionFactory as CategoryAttributeCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Attribute\Collection as CategoryAttributeCollection;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Catalog\Model\Category;
use Magento\Store\Model\Store;
use Magento\Framework\UrlInterface;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\CategoryInterface;

use Emartech\Emarsys\Api\CategoriesApiInterface;
use Emartech\Emarsys\Api\Data\CategoriesApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\CategoriesApiResponseInterface;
use Emartech\Emarsys\Api\Data\CategoryInterfaceFactory;
use Emartech\Emarsys\Api\Data\CategoryStoreDataInterfaceFactory;

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
    private $linkField = '';

    /**
     * @var MetadataPool
     */
    private $metadataPool;

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
     * @param MetadataPool                          $metadataPool
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
        MetadataPool $metadataPool
    ) {
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->categoryAttributeCollectionFactory = $categoryAttributeCollectionFactory;
        $this->categoriesApiResponseFactory = $categoriesApiResponseFactory;
        $this->categoryFactory = $categoryFactory;
        $this->categoryStoreDataFactory = $categoryStoreDataFactory;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param int    $page
     * @param int    $pageSize
     * @param string $storeId
     *
     * @return CategoriesApiResponseInterface
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
            ->setOrder()
            ->setPage($page, $pageSize);

        return $this->categoriesApiResponseFactory->create()->setCurrentPage($this->categoryCollection->getCurPage())
            ->setLastPage($this->categoryCollection->getLastPageNumber())
            ->setPageSize($this->categoryCollection->getPageSize())
            ->setTotalCount($this->categoryCollection->getSize())
            ->setCategories($this->handleCategories());
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
     * @throws \Exception
     */
    private function initCollection()
    {
        $this->categoryCollection = $this->categoryCollectionFactory->create();

        $this->linkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function joinData()
    {
        $this->categoryAttributeCollection = $this->categoryAttributeCollectionFactory->create();
        $this->categoryAttributeCollection
            ->addFieldToFilter('attribute_code', [
                'in' => array_values(array_merge(
                    $this->storeCategoryAttributeCodes,
                    $this->globalCategoryAttributeCodes
                )),
            ]);

        $mainTableName = $this->categoryCollection->getResource()->getTable('catalog_category_entity');

        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $categoryAttribute */
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
     */
    private function setOrder()
    {
        $this->categoryCollection
            ->setOrder($this->linkField, DataCollection::SORT_ORDER_ASC);

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
        $this->categoryCollection->setPage($page, $pageSize);

        return $this;
    }

    /**
     * @return array
     */
    private function handleCategories()
    {
        $returnArray = [];

        foreach ($this->categoryCollection as $category) {
            $returnArray[] = $this->categoryFactory->create()
                ->setPath($category->getPath())
                ->setEntityId($category->getId())
                ->setChildrenCount($category->getChildrenCount())
                ->setStoreData($this->handleCategoryStoreData($category));
        }

        return $returnArray;
    }

    /**
     * @param Category $category
     *
     * @return array
     */
    private function handleCategoryStoreData($category)
    {
        $returnArray = [];

        foreach ($this->storeIds as $storeId => $storeObject) {
            $returnArray[] = $this->categoryStoreDataFactory->create()
                ->setStoreId($storeId)
                ->setIsActive($category->getData($this->getAttributeValueAlias('is_active', $storeId)))
                ->setImage($this->handleImage($category, $storeObject))
                ->setName($category->getData($this->getAttributeValueAlias('name', $storeId)))
                ->setDescription($category->getData($this->getAttributeValueAlias('description', $storeId)));
        }

        return $returnArray;
    }

    /**
     * @param Category $category
     * @param Store    $store
     *
     * @return string
     */
    private function handleImage($category, $store)
    {
        $imagePreUrl = $this->storeIds[0]->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . 'catalog/category/';
        $image = $category->getData($this->getAttributeValueAlias('image', $store->getId()));

        if ($image) {
            return $imagePreUrl . $image;
        }

        return '';
    }
}
