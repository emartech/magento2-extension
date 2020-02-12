<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ProductDeltasApiResponseInterface;

class ProductDeltasApiResponse extends ProductsApiResponse implements ProductDeltasApiResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function getMaxId()
    {
        return $this->getData(self::MAX_ID_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxId($maxId)
    {
        $this->setData(self::MAX_ID_KEY, $maxId);

        return $this;
    }
}
