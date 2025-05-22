<?php

namespace Emartech\Emarsys\CustomerData;

use Magento\Checkout\CustomerData\ItemPool as OriginalItemPool;
use Magento\Quote\Model\Quote\Item;

class ItemPool extends OriginalItemPool
{

    /**
     * Extend getItemData method to add product_price_gross_value
     *
     * @param OriginalItemPool $subject
     * @param array            $result
     * @param Item             $item
     *
     * @return array
     */
    public function afterGetItemData(OriginalItemPool $subject, $result, Item $item)
    {
        $result['product_price_incl_tax'] = (float) $item->getPriceInclTax();
        return $result;
    }
}
