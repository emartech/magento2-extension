<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface ErrorResponseItemInterface
{
    public const EMAIL_KEY       = 'email';
    public const CUSTOMER_ID_KEY = 'customer_id';
    public const MESSAGE_KEY     = 'message';

    /**
     * GetEmail
     *
     * @return string|null
     */
    public function getEmail(): ?string;

    /**
     * GetCustomerId
     *
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * GetMessage
     *
     * @return string|null
     */
    public function getMessage(): ?string;

    /**
     * SetEmail
     *
     * @param string|null $email
     *
     * @return \Emartech\Emarsys\Api\Data\ErrorResponseItemInterface
     */
    public function setEmail(?string $email = null): ErrorResponseItemInterface;

    /**
     * SetCustomerId
     *
     * @param int|null $customerId
     *
     * @return \Emartech\Emarsys\Api\Data\ErrorResponseItemInterface
     */
    public function setCustomerId(?int $customerId = null): ErrorResponseItemInterface;

    /**
     * SetMessage
     *
     * @param string|null $message
     *
     * @return \Emartech\Emarsys\Api\Data\ErrorResponseItemInterface
     */
    public function setMessage(?string $message = null): ErrorResponseItemInterface;
}
