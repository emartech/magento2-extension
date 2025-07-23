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
use Emartech\Emarsys\Model\SettingsFactory;
use Exception;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class Snippets extends Template
{
    /**
     * @var StoreManagerInterface
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
     * @param Context                   $context
     * @param CategoryFactory           $categoryFactory
     * @param Http                      $request
     * @param Registry                  $registry
     * @param ConfigReader              $configReader
     * @param CurrencyFactory           $currencyFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param Configurable              $configurable
     * @param ObjectManagerInterface    $objectManager
     * @param Json                      $jsonHelper
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
     * GetTrackingData
     *
     * @return string
     * @throws Exception
     */
    public function getTrackingData(): string
    {
        return $this->jsonHelper->serialize(
            [
                'product'           => $this->getCurrentProduct() ?? false,
                'category'          => $this->getCategory(0) ?? false,
                'localizedCategory' => $this->getCategory() ?? false,
                'store'             => $this->getStoreData(),
                'search'            => $this->getSearchData() ?? false,
                'exchangeRate'      => $this->getExchangeRate(),
                'slug'              => $this->getStoreSlug()
            ]
        );
    }

    /**
     * GetStoreSlug
     *
     * @return string|null
     * @throws NoSuchEntityException
     */
    public function getStoreSlug(): ?string
    {
        $storeSettings = $this->configReader->getConfigValue(ConfigInterface::STORE_SETTINGS);
        if (is_array($storeSettings)) {
            $currentStoreId = $this->storeManager->getStore()->getId();
            foreach ($storeSettings as $store) {
                if ($store['store_id'] === (int) $currentStoreId) {
                    return $store['slug'];
                }
            }
        }

        return null;
    }

    /**
     * Get Exchange Rate
     *
     * @return float
     * @throws Exception
     */
    public function getExchangeRate(): float
    {
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $baseCurrency = $this->storeManager->getStore()->getBaseCurrency()->getCode();

        return (float) $this->currencyFactory->create()->load($baseCurrency)->getAnyRate($currentCurrency);
    }

    /**
     * GetStoreData
     *
     * @return string[]
     */
    public function getStoreData(): array
    {
        return [
            'merchantId' => $this->getMerchantId(),
        ];
    }

    /**
     * GetCurrentProduct
     *
     * @return array|null
     */
    public function getCurrentProduct(): ?array
    {
        $product = $this->coreRegistry->registry('current_product');
        if ($product instanceof Product) {
            $isVisibleChild = $this->isVisibleChild($product);

            return [
                'sku'            => $product->getSku(),
                'id'             => $product->getId(),
                'isVisibleChild' => $isVisibleChild
            ];
        }

        return null;
    }

    /**
     * GetSearchData
     *
     * @return array|null
     */
    public function getSearchData(): ?array
    {
        $q = $this->_request->getParam('q');
        if ($q != '') {
            return [
                'term' => $q,
            ];
        }

        return null;
    }

    /**
     * GetCategory
     *
     * @param int|null $storeId
     *
     * @return array|null
     *
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCategory(?int $storeId = null): ?array
    {
        $category = $this->coreRegistry->registry('current_category');
        if ($category instanceof CategoryModel) {
            $categoryList = [];

            $categoryIds = $this->removeDefaultCategories($category->getPathIds());

            /** @var Collection $categoryCollection */
            $categoryCollection = $this
                ->categoryCollectionFactory
                ->create()
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

        return null;
    }

    /**
     * RemoveDefaultCategories
     *
     * @param array $categoryIds
     *
     * @return array
     * @throws NoSuchEntityException
     */
    private function removeDefaultCategories(array $categoryIds): array
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
    public function getMerchantId(): string
    {
        $merchantId = $this->configReader->getConfigValue(ConfigInterface::MERCHANT_ID);
        return is_array($merchantId) ? '' : (string) $merchantId;
    }

    /**
     * Get Snippet Url
     *
     * @return string
     */
    public function getSnippetUrl(): string
    {
        $snippetUrl = $this->configReader->getConfigValue(ConfigInterface::SNIPPET_URL);
        return is_array($snippetUrl) ? '' : (string) $snippetUrl;
    }

    /**
     * Is Injectable
     *
     * @return bool
     */
    public function isInjectable(): bool
    {
        return $this->configReader->isEnabledForStore(ConfigInterface::INJECT_WEBEXTEND_SNIPPETS);
    }

    /**
     * IsVisibleChild
     *
     * @param Product $product
     *
     * @return bool
     */
    private function isVisibleChild(Product $product): bool
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
