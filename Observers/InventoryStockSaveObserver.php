<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Observers;

use Emartech\Emarsys\Helper\ProductDelta as ProductDeltaHelper;
use Magento\CatalogInventory\Model\Stock\Item as StockItem;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class InventoryStockSaveObserver implements ObserverInterface
{
    /**
     * @var ProductDeltaHelper
     */
    private $productDeltaHelper;

    /**
     * InventoryStockSaveObserver constructor.
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
        /** @var StockItem $stockItem */
        $stockItem = $observer->getData('data_object');
        $this->productDeltaHelper->createDelta($stockItem->getProductId());
    }
}
