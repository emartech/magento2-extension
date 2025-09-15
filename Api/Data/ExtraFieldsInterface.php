<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface ExtraFieldsInterface
{
    public const KEY_KEY        = 'key';
    public const VALUE_KEY      = 'value';
    public const TEXT_VALUE_KEY = 'text_value';

    /**
     * GetKey
     *
     * @return string|null
     */
    public function getKey(): ?string;

    /**
     * GetValue
     *
     * @return string|null
     */
    public function getValue(): ?string;

    /**
     * GetTextValue
     *
     * @return string|null
     */
    public function getTextValue(): ?string;

    /**
     * SetKey
     *
     * @param string|null $key
     *
     * @return \Emartech\Emarsys\Api\Data\ExtraFieldsInterface
     */
    public function setKey(?string $key = null): ExtraFieldsInterface;

    /**
     * SetValue
     *
     * @param string|null $value
     *
     * @return \Emartech\Emarsys\Api\Data\ExtraFieldsInterface
     */
    public function setValue(?string $value = null): ExtraFieldsInterface;

    /**
     * SetTextValue
     *
     * @param string|null $textValue
     *
     * @return \Emartech\Emarsys\Api\Data\ExtraFieldsInterface
     */
    public function setTextValue(?string $textValue = null): ExtraFieldsInterface;
}
