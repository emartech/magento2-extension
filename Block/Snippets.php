<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */

namespace Emartech\Emarsys\Block;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Emartech\Emarsys\Model\SettingsFactory;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Catalog\Api\Data\CategoryInterface;

/**
 * Class Snippets
 * @package Emartech\Emarsys\Block
 */
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
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var CategoryCollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * Snippets constructor.
     *
     * @param Context                   $context
     * @param CategoryFactory           $categoryFactory
     * @param Http                      $request
     * @param Registry                  $registry
     * @param ConfigReader              $configReader
     * @param CurrencyFactory           $currencyFactory
     * @param JsonSerializer            $jsonSerializer
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param MetadataPool              $metadataPool
     * @param array                     $data
     */
    public function __construct(
        Context $context,
        CategoryFactory $categoryFactory,
        Http $request,
        Registry $registry,
        ConfigReader $configReader,
        CurrencyFactory $currencyFactory,
        JsonSerializer $jsonSerializer,
        CategoryCollectionFactory $categoryCollectionFactory,
        MetadataPool $metadataPool,
        array $data = []
    ) {
        $this->storeManager = $context->getStoreManager();
        $this->categoryFactory = $categoryFactory;
        $this->_request = $request;
        $this->coreRegistry = $registry;
        $this->configReader = $configReader;
        $this->currencyFactory = $currencyFactory;
        $this->jsonSerializer = $jsonSerializer;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->metadataPool = $metadataPool;
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
        return [
            'product'      => $this->getCurrentProduct(),
            'category'     => $this->getCategory(),
            'store'        => $this->getStoreData(),
            'search'       => $this->getSearchData(),
            'exchangeRate' => $this->getExchangeRate(),
            'slug'         => $this->getStoreSlug(),
        ];
    }

    /**
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreSlug()
    {
        $storeSettings = $this->jsonSerializer
            ->unserialize($this->configReader->getConfigValue(ConfigInterface::STORE_SETTINGS));
        $currentStoreId = $this->storeManager->getStore()->getId();
        foreach ($storeSettings as $store) {
            if ($store['store_id'] === (int)$currentStoreId) {
                return $store['slug'];
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
                return [
                    'sku' => $product->getSku(),
                    'id'  => $product->getId(),
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
    public function getCategory()
    {
        try {
            $category = $this->coreRegistry->registry('current_category');
            if ($category instanceof CategoryModel) {
                $categoryList = [];

                $categoryIds = $this->removeDefaultCategories($category->getPathIds());

                $linkField = $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();

                /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
                $categoryCollection = $this->categoryCollectionFactory->create()
                    ->setStore($this->storeManager->getStore())
                    ->addAttributeToSelect('name')
                    ->addFieldToFilter($linkField, ['in' => $categoryIds]);

                /** @var Category $category */
                foreach ($categoryCollection as $categoryItem) {
                    $categoryList[] = $categoryItem->getName();
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
}
