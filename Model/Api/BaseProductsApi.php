<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Helper\Product as ProductHelper;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Webapi\Exception as WebApiException;
use Magento\Store\Model\StoreManagerInterface;
use Emartech\Emarsys\Helper\LinkField as LinkFieldHelper;

class BaseProductsApi
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /**
     * @var LinkFieldHelper
     */
    protected $linkFieldHelper;

    /**
     * @var string
     */
    protected $linkField;

    /**
     * @var int[]
     */
    protected $customerGroups = [0];

    /**
     * @var int[]
     */
    protected $storeIds = [];

    /**
     * @var array
     */
    protected $websiteIds = [];

    /**
     * @var int
     */
    protected $numberOfItems = 0;

    /**
     * @var int
     */
    protected $minId = 0;

    /**
     * @var int
     */
    protected $maxId = 0;

    /**
     * BaseProductsApi constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ProductHelper         $productHelper
     * @param LinkFieldHelper       $linkFieldHelper
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductHelper $productHelper,
        LinkFieldHelper $linkFieldHelper
    ) {
        $this->storeManager = $storeManager;
        $this->productHelper = $productHelper;
        $this->linkFieldHelper = $linkFieldHelper;
        $this->linkField = $this->linkFieldHelper->getEntityLinkField(ProductInterface::class);
    }

    /**
     * @param mixed $storeIds
     *
     * @return $this
     * @throws WebApiException
     */
    // @codingStandardsIgnoreLine
    protected function initStores($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = explode(',', $storeIds);
        }

        $availableStores = $this->storeManager->getStores(true);

        foreach ($availableStores as $availableStore) {
            $storeId = (int)$availableStore->getId();
            if (in_array($storeId, $storeIds)) {
                $this->storeIds[$storeId] = $availableStore;
                $websiteId = (int)$availableStore->getWebsiteId();
                if ($websiteId) {
                    if (!array_key_exists($websiteId, $this->websiteIds)) {
                        $this->websiteIds[$websiteId] = [];
                    }
                    $this->websiteIds[$websiteId][] = $storeId;
                }
            }
        }

        if (!array_key_exists(0, $this->storeIds)) {
            throw new WebApiException(__('Store ID must contain 0'));
        }

        return $this;
    }

    /**
     * @param $productCollection
     *
     * @return array
     */
    // @codingStandardsIgnoreLine
    protected function handleProducts($productCollection)
    {
        $returnArray = [];

        /** @var Product $product */
        foreach ($productCollection as $product) {
            $returnArray[] = $this->productHelper->buildProductObject($product,
                $this->storeIds, $this->linkField);
        }

        return $returnArray;
    }
}
