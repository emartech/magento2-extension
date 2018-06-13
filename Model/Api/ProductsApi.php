<?php

namespace Emartech\Emarsys\Model\Api;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Emartech\Emarsys\Api\ProductsApiInterface;

class ProductsApi implements ProductsApiInterface
{
  /**
   * @var productFactory
   */
  protected $productFactory;

  /**
   * @var CategoryFactory
   */
  protected $categoryFactory;

  /**
   * @var StoreManagerInterface
   */
  protected $storeManagerInterface;

  /**
   * Product constructor.
   * @param ProductFactory $productFactory
   * @param CategoryFactory $categoryFactory
   * @param StoreManagerInterface $storeManagerInterface
   */
  public function __construct(
    ProductFactory $productFactory,
    CategoryFactory $categoryFactory,
    StoreManagerInterface $storeManagerInterface
  ) {
    $this->productFactory = $productFactory;
    $this->categoryFactory = $categoryFactory;
    $this->storeManagerInterface = $storeManagerInterface;
  }

  /**
   * @param int $page
   * @param int $page_size
   * @return mixed
   */
  public function get($page, $page_size)
  {
    $productCollection = $this->productFactory->create()
      ->getCollection()
      ->addAttributeToSelect(['name', 'price', 'image', 'small_image', 'thumbnail', 'description', 'type_id'])
      ->joinTable('cataloginventory_stock_item', 'product_id=entity_id', ['qty', 'is_in_stock'], '{{table}}.stock_id=1', 'left')
      ->setCurPage($page)
      ->setPageSize($page_size);
    $responseProducts = [];
    foreach ($productCollection as $product) {
      $productCategories = $this->getProductCategories($product);
      $productImageUrls = $this->getProductImageUrls($product);

      $childrenEntityIds = $this->getChildrenEntityids($product);

      $responseProducts[] = [
        'type' => $product['type_id'],
        'entity_id' => $product['entity_id'],
        'children_entity_ids' => $childrenEntityIds,
        'categories' => $productCategories,
        'sku' => $product['sku'],
        'name' => $product['name'],
        'price' => $product['price'],
        'link' => $product->getProductUrl(),
        'images' => $productImageUrls,
        'qty' => $product['qty'],
        'is_in_stock' => $product['is_in_stock'],
        'description' => $product['description'],
      ];
    }
    $responseData = [[
      'products' => $responseProducts,
      'page' => $page,
      'page_size' => $page_size
    ]];

    return $responseData;
  }

  /**
   * @param $product
   * @return array
   */
  private function getProductCategories($product)
  {
    $categories = [];
    $categoryIds = $product->getCategoryIds();
    foreach ($categoryIds as $categoryId) {

      $categoryData = $this->categoryFactory->create()->load($categoryId);
      $categoryPathIds = array_slice(explode('/', $categoryData->getPath()), 2);
      if (count($categoryPathIds) > 0) {
        $categoryPathNames = [];
        foreach ($categoryPathIds as $categoryPathId) {
          $categoryPathNames[] = $this->categoryFactory->create()->load($categoryPathId)->getName();
        }
        $categories[] = $categoryPathNames;
      }
    }
    return $categories;
  }

  /**
   * @param $product
   * @return array
   */
  private function getProductImageUrls($product)
  {
    $store = $this->storeManagerInterface->getStore();
    $mediaBaseUrl = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    return [
      'image' => $mediaBaseUrl . 'catalog/product' . $product['image'],
      'small_image' => $mediaBaseUrl . 'catalog/product' . $product['small_image'],
      'thumbnail' => $mediaBaseUrl . 'catalog/product' . $product['thumbnail']
    ];
  }

  /**
   * @param $product
   * @return array
   */
  private function getChildrenEntityids($product)
  {
    if ($product['type_id'] !== 'configurable') {
      return [];
    }
    $children = $product->getTypeInstance()->getUsedProducts($product);
    $responseChildren = [];
    foreach($children as $child) {
      $responseChildren[] = $child->getId();
    }
    return $responseChildren;
  }
}


