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
     * GetEmail
     *
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getData(self::EMAIL_KEY);
    }

    /**
     * GetCustomerId
     *
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->getData(self::CUSTOMER_ID_KEY);
    }

    /**
     * GetMessage
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->getData(self::MESSAGE_KEY);
    }

    /**
     * SetEmail
     *
     * @param string|null $email
     *
     * @return ErrorResponseItemInterface
     */
    public function setEmail(?string $email = null): ErrorResponseItemInterface
    {
        $this->setData(self::EMAIL_KEY, $email);

        return $this;
    }

    /**
     * SetCustomerId
     *
     * @param int|null $customerId
     *
     * @return ErrorResponseItemInterface
     */
    public function setCustomerId(?int $customerId = null): ErrorResponseItemInterface
    {
        $this->setData(self::CUSTOMER_ID_KEY, $customerId);

        return $this;
    }

    /**
     * SetMessage
     *
     * @param string|null $message
     *
     * @return ErrorResponseItemInterface
     */
    public function setMessage(?string $message = null): ErrorResponseItemInterface
    {
        $this->setData(self::MESSAGE_KEY, $message);

        return $this;
    }
}
