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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME_KEY);
    }

    /**
     * GetIsSystem
     *
     * @return bool|null
     */
    public function getIsSystem(): ?bool
    {
        return $this->getData(self::IS_SYSTEM_KEY);
    }

    /**
     * GetIsVisible
     *
     * @return bool|null
     */
    public function getIsVisible(): ?bool
    {
        return $this->getData(self::IS_VISIBLE_KEY);
    }

    /**
     * GetIsVisibleOnFront
     *
     * @return bool|null
     */
    public function getIsVisibleOnFront(): ?bool
    {
        return $this->getData(self::IS_VISIBLE_ON_FRONT_KEY);
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
     * @param string|null $name
     *
     * @return AttributeInterface
     */
    public function setName(?string $name = null): AttributeInterface
    {
        $this->setData(self::NAME_KEY, $name);

        return $this;
    }

    /**
     * SetIsSystem
     *
     * @param bool|null $isSystem
     *
     * @return AttributeInterface
     */
    public function setIsSystem(?bool $isSystem = null): AttributeInterface
    {
        $this->setData(self::IS_SYSTEM_KEY, $isSystem);

        return $this;
    }

    /**
     * SetIsVisible
     *
     * @param bool|null $isVisible
     *
     * @return AttributeInterface
     */
    public function setIsVisible(?bool $isVisible = null): AttributeInterface
    {
        $this->setData(self::IS_VISIBLE_KEY, $isVisible);

        return $this;
    }

    /**
     * SetIsVisibleOnFront
     *
     * @param bool|null $isVisibleOnFront
     *
     * @return AttributeInterface
     */
    public function setIsVisibleOnFront(?bool $isVisibleOnFront = null): AttributeInterface
    {
        $this->setData(self::IS_VISIBLE_ON_FRONT_KEY, $isVisibleOnFront);

        return $this;
    }
}
