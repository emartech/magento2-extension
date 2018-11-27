<?php
/**
 * Copyright ©2018 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\ResourceModel\Api;

use Magento\Customer\Model\ResourceModel\Customer as CustomerResourceModel;
use Magento\Customer\Model\ResourceModel\Customer\Collection;

/**
 * Class Customer
 * @package Emartech\Emarsys\Model\ResourceModel\Api
 */
class Customer extends CustomerResourceModel
{
    /**
     * @param Collection $collection
     *
     * @return void
     */
    public function joinSubscriptionStatus($collection)
    {
        $subSelect = $this->_resource->getConnection()->select()
            ->from($this->getTable('newsletter_subscriber'), ['subscriber_status'])
            ->where('customer_id = e.entity_id')
            ->order('subscriber_id DESC')
            ->limit(1, 0);

        $collection->getSelect()->columns([
            'accepts_marketing' => $subSelect,
        ]);
    }
}
