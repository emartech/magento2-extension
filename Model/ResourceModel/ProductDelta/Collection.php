<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\ResourceModel\ProductDelta;

use Emartech\Emarsys\Model\ProductDelta as ProductDeltaModel;
use Emartech\Emarsys\Model\ResourceModel\ProductDelta as ProductDeltaResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    // @codingStandardsIgnoreLine
    protected $_idFieldName = 'product_delta_id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(
            ProductDeltaModel::class,
            ProductDeltaResourceModel::class
        );
    }
}
