<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ErrorResponseItemInterface;
use Magento\Framework\DataObject;

class ErrorResponseItem extends DataObject implements ErrorResponseItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $this->setData(self::EMAIL_KEY, $email);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        $this->setData(self::CUSTOMER_ID_KEY, $customerId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($message)
    {
        $this->setData(self::MESSAGE_KEY, $message);

        return $this;
    }
}
