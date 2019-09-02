<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface AttributesApiResponseInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface AttributesApiResponseInterface
{
    const ATTRIBUTES_KEY = 'attributes';

    /**
     * @return \Emartech\Emarsys\Api\Data\AttributeInterface[]
     */
    public function getAttributes();

    /**
     * @param \Emartech\Emarsys\Api\Data\AttributeInterface[] $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes);
}