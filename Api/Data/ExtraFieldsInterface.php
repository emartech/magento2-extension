<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface ExtraFieldsInterface
{
    const KEY_KEY        = 'key';
    const VALUE_KEY      = 'value';
    const TEXT_VALUE_KEY = 'text_value';

    /**
     * @return string
     */
    public function getKey();

    /**
     * @return string
     */
    public function getValue();

    /**
     * @return string|null
     */
    public function getTextValue();

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

    /**
     * @param string $textValue
     *
     * @return $this
     */
    public function setTextValue($textValue);
}
