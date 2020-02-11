<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api;

interface ProductDeltasApiInterface
{
    /**
     * @param int      $page
     * @param int      $pageSize
     * @param string   $storeId
     * @param int      $sinceId
     * @param int|null $maxId
     *
     * @return \Emartech\Emarsys\Api\Data\ProductDeltasApiResponseInterface
     *
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function get($page, $pageSize, $storeId, $sinceId, $maxId = null);
}