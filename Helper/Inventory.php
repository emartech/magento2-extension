<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;

class Inventory extends AbstractHelper
{
    /**
     * @return \Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory|bool
     */
    public function getSourceItemCollectionFactory()
    {
        if (class_exists(\Magento\Inventory\Model\ResourceModel\SourceItem\Collection::class)) {
            $objManager = ObjectManager::getInstance();
            return $objManager->create(\Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory::class);
        }

        return false;
    }
}
