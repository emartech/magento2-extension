<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */
namespace Emartech\Emarsys\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;

/**
 * Class Snippets
 * @package Emartech\Emarsys\Block
 */
class Snippets extends \Magento\Framework\View\Element\Template
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
   * Snippets constructor.
   *
   * @param Context $context
   * @param CategoryFactory $categoryFactory
   * @param Http $request
   * @param Registry $registry
   * @param array $data
   */
  public function __construct(
    Context $context,
    CategoryFactory $categoryFactory,
    Http $request,
    Registry $registry,
    array $data = []
  ) {
    $this->storeManager = $context->getStoreManager();
    $this->categoryFactory = $categoryFactory;
    $this->_request = $request;
    $this->coreRegistry = $registry;
    parent::__construct($context, $data);
  }

  /**
   * Get Sku
   *
   * @return string
   */
  public function getSku()
  {
    try {
      $product = $this->coreRegistry->registry('current_product');
      if (isset($product) && $product != '') {
        return addslashes($product->getSku());
      }
    } catch (\Exception $e) {
      throw $e;
    }

    return false;
  }

  /**
   * Get Search Term
   *
   * @return bool|mixed
   */
  public function getSearchTerm()
  {
    try {
      $q = $this->_request->getParam('q');
      if ($q != '') {
        return $q;
      }
    } catch (\Exception $e) {
      throw $e;
    }
    return false;
  }

  /**
   * Get Category
   *
   * @return string
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
        return implode(" > ", $categoryList);
      }
    } catch (\Exception $e) {
      throw $e;
    }
    return false;
  }
}