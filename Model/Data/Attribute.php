<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\AttributeInterface;
use Magento\Framework\DataObject;

class Attribute extends DataObject implements AttributeInterface
{
    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getData(self::CODE_KEY);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME_KEY);
    }

    /**
     * @return bool
     */
    public function getIsSystem()
    {
        return $this->getData(self::IS_SYSTEM_KEY);
    }

    /**
     * @return bool
     */
    public function getIsVisible()
    {
        return $this->getData(self::IS_VISIBLE_KEY);
    }

    /**
     * @return bool
     */
    public function getIsVisibleOnFront()
    {
        return $this->getData(self::IS_VISIBLE_ON_FRONT_KEY);
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->setData(self::CODE_KEY, $code);

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->setData(self::NAME_KEY, $name);

        return $this;
    }

    /**
     * @param bool $isSystem
     *
     * @return $this
     */
    public function setIsSystem($isSystem)
    {
        $this->setData(self::IS_SYSTEM_KEY, $isSystem);

        return $this;
    }

    /**
     * @param bool $isVisible
     *
     * @return $this
     */
    public function setIsVisible($isVisible)
    {
        $this->setData(self::IS_VISIBLE_KEY, $isVisible);

        return $this;
    }

    /**
     * @param bool $isVisibleOnFront
     *
     * @return $this
     */
    public function setIsVisibleOnFront($isVisibleOnFront)
    {
        $this->setData(self::IS_VISIBLE_ON_FRONT_KEY, $isVisibleOnFront);

        return $this;
    }
}
