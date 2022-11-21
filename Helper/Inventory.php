<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Inventory\Model\ResourceModel\SourceItem\Collection;
use Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory;

class Inventory extends AbstractHelper
{
    // @codingStandardsIgnoreStart
    /**
     * GetSourceItemCollectionFactory
     *
     * @return CollectionFactory|null
     */
    public function getSourceItemCollectionFactory(): ?CollectionFactory
    {
        if (class_exists(Collection::class)) {
            return ObjectManager::getInstance()->create(CollectionFactory::class);
        }

        return null;
    }
    // @codingStandardsIgnoreEnd
}
