<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface ErrorResponseItemInterface
{
    const EMAIL_KEY       = 'email';
    const CUSTOMER_ID_KEY = 'customer_id';
    const MESSAGE_KEY     = 'message';

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email);

    /**
     * @param int|null $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message);
}
