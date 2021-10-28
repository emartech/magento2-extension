<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ExtraFieldsInterface;
use Magento\Framework\DataObject;

class ExtraFields extends DataObject implements ExtraFieldsInterface
{
    /**
     * @return string
     */
    public function getKey()
    {
        return $this->getData(self::KEY_KEY);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getData(self::VALUE_KEY);
    }

    /**
     * @return string
     */
    public function getTextValue()
    {
        return $this->getData(self::TEXT_VALUE_KEY);
    }

    /**
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->setData(self::KEY_KEY, $key);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->setData(self::VALUE_KEY, $value);

        return $this;
    }

    /**
     * @param string $textValue
     *
     * @return $this
     */
    public function setTextValue($textValue)
    {
        $this->setData(self::TEXT_VALUE_KEY, $textValue);

        return $this;
    }
}
