<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */
namespace Emartech\Emarsys\Block;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Emartech\Emarsys\Model\SettingsFactory;

/**
 * Class Snippets
 * @package Emartech\Emarsys\Block
 */
class Snippets extends Template
{
  /**
   * @var \Magento\Store\Model\StoreManagerInterface
   */
  protected $storeManager;

  /**
   * @var CategoryFactory
   */
  protected $categoryFactory;

  /**
   * @var Registry
   */
  protected $coreRegistry;

  /**
   * @var ConfigReader
   */
  private $configReader;

  /**
   * Snippets constructor.
   *
   * @param Context $context
   * @param CategoryFactory $categoryFactory
   * @param Http $request
   * @param Registry $registry
   * @param ConfigReader $configReader
   * @param array $data
   */
  public function __construct(
    Context $context,
    CategoryFactory $categoryFactory,
    Http $request,
    Registry $registry,
    ConfigReader $configReader,
    array $data = []
  ) {
    $this->storeManager = $context->getStoreManager();
    $this->categoryFactory = $categoryFactory;
    $this->_request = $request;
    $this->coreRegistry = $registry;
    $this->configReader = $configReader;
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
      'product' => $this->getCurrentProduct(),
      'category' => $this->getCategory(),
      'store' => $this->getStoreData(),
      'search' => $this->getSearchData()
    ];
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
        'merchantId' => $this->getMerchantId()
      ];
    } catch (\Exception $e) {
      throw $e;
    }

    return false;
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
      if (isset($product) && $product != '') {
        return [
          'sku' => $product->getSku(),
          'id' => $product->getId()
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
          'term' => $q
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
      if (isset($category) && $category != '') {
        $categoryPath = $category->getPath();
        $categoryIds = explode('/', $categoryPath);
        $categoryList = [];
        for ($pathIndex = 2; $pathIndex < count($categoryIds); $pathIndex++) {
          $storeId = $this->storeManager->getDefaultStoreView()->getId();
          $childCat = $this->categoryFactory->create()->setStoreId($storeId)->load($categoryIds[$pathIndex]);
          $categoryList[] = $childCat->getName();
        }
        return [
          'names' => $categoryList,
          'ids' => array_splice($categoryIds, 2)
        ];
      }
    } catch (\Exception $e) {
      throw $e;
    }
    return false;
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
    return $this->configReader->isEnabledForWebsite(ConfigInterface::INJECT_WEBEXTEND_SNIPPETS);
  }
}