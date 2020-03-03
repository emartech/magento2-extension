<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\ProductDelta as ProductDeltaHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductAttributesUpdateObserver implements ObserverInterface
{
    /**
     * @var ProductDeltaHelper
     */
    private $productDeltaHelper;

    /**
     * CatalogProductSaveObserver constructor.
     *
     * @param ProductDeltaHelper $productDeltaHelper
     */
    public function __construct(
        ProductDeltaHelper $productDeltaHelper
    ) {
        $this->productDeltaHelper = $productDeltaHelper;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $productIds = $observer->getData('product_ids');
        foreach ($productIds as $productId) {
            $this->productDeltaHelper->createDelta($productId);
        }
    }
}
