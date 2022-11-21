<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\AttributeInterface;
use Magento\Framework\DataObject;

class Attribute extends DataObject implements AttributeInterface
{
    /**
     * GetCode
     *
     * @return string
     */
    public function getCode(): string
    {
        return (string) $this->getData(self::CODE_KEY);
    }

    /**
     * GetName
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->getData(self::NAME_KEY);
    }

    /**
     * GetIsSystem
     *
     * @return bool
     */
    public function getIsSystem(): bool
    {
        return (bool) $this->getData(self::IS_SYSTEM_KEY);
    }

    /**
     * GetIsVisible
     *
     * @return bool
     */
    public function getIsVisible(): bool
    {
        return (bool) $this->getData(self::IS_VISIBLE_KEY);
    }

    /**
     * GetIsVisibleOnFront
     *
     * @return bool
     */
    public function getIsVisibleOnFront(): bool
    {
        return (bool) $this->getData(self::IS_VISIBLE_ON_FRONT_KEY);
    }

    /**
     * SetCode
     *
     * @param string $code
     *
     * @return AttributeInterface
     */
    public function setCode(string $code): AttributeInterface
    {
        $this->setData(self::CODE_KEY, $code);

        return $this;
    }

    /**
     * SetName
     *
     * @param string $name
     *
     * @return AttributeInterface
     */
    public function setName(string $name): AttributeInterface
    {
        $this->setData(self::NAME_KEY, $name);

        return $this;
    }

    /**
     * SetIsSystem
     *
     * @param bool $isSystem
     *
     * @return AttributeInterface
     */
    public function setIsSystem(bool $isSystem): AttributeInterface
    {
        $this->setData(self::IS_SYSTEM_KEY, $isSystem);

        return $this;
    }

    /**
     * SetIsVisible
     *
     * @param bool $isVisible
     *
     * @return AttributeInterface
     */
    public function setIsVisible(bool$isVisible): AttributeInterface
    {
        $this->setData(self::IS_VISIBLE_KEY, $isVisible);

        return $this;
    }

    /**
     * SetIsVisibleOnFront
     *
     * @param bool $isVisibleOnFront
     *
     * @return AttributeInterface
     */
    public function setIsVisibleOnFront(bool $isVisibleOnFront): AttributeInterface
    {
        $this->setData(self::IS_VISIBLE_ON_FRONT_KEY, $isVisibleOnFront);

        return $this;
    }
}
