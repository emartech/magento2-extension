<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\AttributeInterface;
use Emartech\Emarsys\Api\Data\AttributesApiResponseInterface;
use Magento\Framework\DataObject;

class AttributesApiResponse extends DataObject implements AttributesApiResponseInterface
{
    /**
     * GetAttributes
     *
     * @return AttributeInterface[]
     */
    public function getAttributes(): array
    {
        return $this->getData(self::ATTRIBUTES_KEY);
    }

    /**
     * SetAttributes
     *
     * @param AttributeInterface[] $attributes
     *
     * @return AttributesApiResponseInterface
     */
    public function setAttributes(array $attributes): AttributesApiResponseInterface
    {
        $this->setData(self::ATTRIBUTES_KEY, $attributes);

        return $this;
    }
}
