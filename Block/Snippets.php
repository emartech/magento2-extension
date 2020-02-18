<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */

namespace Emartech\Emarsys\Block;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Emartech\Emarsys\Helper\Json;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Emartech\Emarsys\Model\SettingsFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\ObjectManagerInterface;

class Snippets extends Template
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * @var CurrencyFactory
     */
    private $currencyFactory;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var Configurable
     */
    private $configurable;
    /**
     * @var Json
     */
    private $jsonHelper;

    /**
     * Snippets constructor.
     *
     * @param Context                   $context
     * @param CategoryFactory           $categoryFactory
     * @param Http                      $request
     * @param Registry                  $registry
     * @param ConfigReader              $configReader
     * @param CurrencyFactory           $currencyFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param ObjectManagerInterface    $objectManager
     * @param array                     $data
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        Http $request,
        Registry $registry,
        ConfigReader $configReader,
        CurrencyFactory $currencyFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Configurable $configurable,
        ObjectManagerInterface $objectManager,
        Json $jsonHelper,
        array $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        $this->categoryFactory = $categoryFactory;
        $this->_request = $request;
        $this->coreRegistry = $registry;
        $this->configReader = $configReader;
        $this->currencyFactory = $currencyFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->configurable = $configurable;
        $this->objectManager = $objectManager;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get Tracking Data
     *
     * @return mixed
     * @throws \Exception
     */
    public function getTrackingData()
    {
        return $this->jsonHelper->serialize([
            'product'            => $this->getCurrentProduct(),
            'category'           => $this->getCategory(0),
            'localizedCategory' => $this->getCategory(),
            'store'              => $this->getStoreData(),
            'search'             => $this->getSearchData(),
            'exchangeRate'       => $this->getExchangeRate(),
            'slug'               => $this->getStoreSlug()
        ]);
    }

    /**
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreSlug()
    {
        $storeSettings = $this->configReader->getConfigValue(ConfigInterface::STORE_SETTINGS);
        if (is_array($storeSettings)) {
            $currentStoreId = $this->storeManager->getStore()->getId();
            foreach ($storeSettings as $store) {
                if ($store['store_id'] === (int)$currentStoreId) {
                    return $store['slug'];
                }
            }
        }
        return null;
    }

    /**
     * Get Exchange Rate
     *
     * @return bool|float
     * @throws \Exception
     */
    public function getExchangeRate()
    {
        try {
            $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
            $baseCurrency = $this->storeManager->getStore()->getBaseCurrency()->getCode();
            return (float)$this->currencyFactory->create()->load($baseCurrency)->getAnyRate($currentCurrency);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Store Data
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function getStoreData()
    {
        try {
            return [
                'merchantId' => $this->getMerchantId(),
            ];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Get Current Product
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function getCurrentProduct()
    {
        try {
            $product = $this->coreRegistry->registry('current_product');
            if ($product instanceof Product) {
                $isVisibleChild = $this->isVisibleChild($product);
                return [
                    'sku' => $product->getSku(),
                    'id'  => $product->getId(),
                    'isVisibleChild' => $isVisibleChild
                ];
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return false;
    }

    /**
     * Get Search Data
     *
     * @return bool|mixed
     * @throws \Exception
     */
    public function getSearchData()
    {
        try {
            $q = $this->_request->getParam('q');
            if ($q != '') {
                return [
                    'term' => $q,
                ];
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return false;
    }

    /**
     * Get Category
     *
     * @return mixed
     * @throws \Exception
     */
    public function getCategory($storeId = null)
    {
        try {
            $category = $this->coreRegistry->registry('current_category');
            if ($category instanceof CategoryModel) {
                $categoryList = [];

                $categoryIds = $this->removeDefaultCategories($category->getPathIds());

                /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
                $categoryCollection = $this->categoryCollectionFactory->create()
                    ->setStore($this->storeManager->getStore($storeId))
                    ->addAttributeToSelect('name')
                    ->addFieldToFilter('entity_id', ['in' => $categoryIds]);

                foreach ($categoryIds as $categoryId) {
                    foreach ($categoryCollection as $categoryItem) {
                        if ($categoryItem->getId() == $categoryId) {
                            $categoryList[] = $categoryItem->getName();
                        }
                    }
                }

                return [
                    'names' => $categoryList,
                    'ids'   => $categoryIds,
                ];
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return false;
    }

    /**
     * @param array $categoryIds
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function removeDefaultCategories($categoryIds)
    {
        $returnArray = [];
        $basicCategoryIds = [
            1,
            $this->storeManager->getStore()->getRootCategoryId(),
        ];
        foreach ($categoryIds as $categoryId) {
            if (!in_array($categoryId, $basicCategoryIds)) {
                $returnArray[] = $categoryId;
            }
        }

        return $returnArray;
    }

    /**
     * Get Merchant ID
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->configReader->getConfigValue(ConfigInterface::MERCHANT_ID);
    }

    /**
     * Get Snippet Url
     *
     * @return string
     */
    public function getSnippetUrl()
    {
        return $this->configReader->getConfigValue(ConfigInterface::SNIPPET_URL);
    }

    /**
     * Is Injectable
     *
     * @return string
     */
    public function isInjectable()
    {
        return $this->configReader->isEnabledForStore(ConfigInterface::INJECT_WEBEXTEND_SNIPPETS);
    }

    /**
     * @param Product $product
     * @return bool
     */
    private function isVisibleChild(Product $product)
    {
        $productId = $product->getId();
        $productVisibility = $product->getVisibility();
        $visibleInCatalogOrSearch = [2, 4];
        if ($product->getTypeId() === 'simple' && in_array($productVisibility, $visibleInCatalogOrSearch, false)) {
            $parentConfigObject = $this->configurable->getParentIdsByChild($productId);
            if (!empty($parentConfigObject)) {
                return true;
            }
        }
        return false;
    }
}
