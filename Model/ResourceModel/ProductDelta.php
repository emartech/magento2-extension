<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Exception;

class ProductDelta extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('emarsys_product_delta', 'product_delta_id');
    }

    /**
     * @param int $sinceId
     *
     * @return bool
     */
    public function isSinceIdIsHigherThanAutoIncrement($sinceId)
    {
        try {
            return (bool)$this->getConnection()->fetchOne("
            SELECT
                (
                    SELECT
                        `AUTO_INCREMENT`
                    FROM
                        INFORMATION_SCHEMA.TABLES
                    WHERE
                        TABLE_SCHEMA = (
                            SELECT
                                database()
                        )
                        AND TABLE_NAME = ?
                ) <= (
                    SELECT
                        CAST(? AS UNSIGNED)
                );
        ",
                [
                    $this->getMainTable(),
                    $sinceId,
                ]
            );
        } catch (Exception $e) {
            return true;
        }
    }
}
