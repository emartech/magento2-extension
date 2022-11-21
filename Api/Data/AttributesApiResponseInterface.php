<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface AttributesApiResponseInterface
{
    public const ATTRIBUTES_KEY = 'attributes';

    /**
     * GetAttributes
     *
     * @return \Emartech\Emarsys\Api\Data\AttributeInterface[]
     */
    public function getAttributes(): array;

    /**
     * SetAttributes
     *
     * @param \Emartech\Emarsys\Api\Data\AttributeInterface[] $attributes
     *
     * @return \Emartech\Emarsys\Api\Data\AttributesApiResponseInterface
     */
    public function setAttributes(array $attributes): AttributesApiResponseInterface;
}
