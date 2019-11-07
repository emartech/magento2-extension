<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface ExtraFieldsInterface
{
    const KEY_KEY   = 'key';
    const VALUE_KEY = 'value';

    /**
     * @return string
     */
    public function getKey();

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key);

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value);
}
