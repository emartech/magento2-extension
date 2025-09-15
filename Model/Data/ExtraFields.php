<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ExtraFieldsInterface;
use Magento\Framework\DataObject;

class ExtraFields extends DataObject implements ExtraFieldsInterface
{
    /**
     * GetKey
     *
     * @return string|null
     */
    public function getKey(): ?string
    {
        return $this->getData(self::KEY_KEY);
    }

    /**
     * GetValue
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->getData(self::VALUE_KEY);
    }

    /**
     * GetTextValue
     *
     * @return string|null
     */
    public function getTextValue(): ?string
    {
        return $this->getData(self::TEXT_VALUE_KEY);
    }

    /**
     * SetKey
     *
     * @param string|null $key
     *
     * @return ExtraFieldsInterface
     */
    public function setKey(?string $key = null): ExtraFieldsInterface
    {
        $this->setData(self::KEY_KEY, $key);

        return $this;
    }

    /**
     * SetValue
     *
     * @param string|null $value
     *
     * @return ExtraFieldsInterface
     */
    public function setValue(?string $value = null): ExtraFieldsInterface
    {
        $this->setData(self::VALUE_KEY, $value);

        return $this;
    }

    /**
     * SetTextValue
     *
     * @param string|null $textValue
     *
     * @return ExtraFieldsInterface
     */
    public function setTextValue(?string $textValue = null): ExtraFieldsInterface
    {
        $this->setData(self::TEXT_VALUE_KEY, $textValue);

        return $this;
    }
}
