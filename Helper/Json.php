<?php
/**
 * Copyright ©2018 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class Json
 * @package Emartech\Emarsys\Helper
 */
class Json extends AbstractHelper
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * Json constructor.
     *
     * @param ProductMetadataInterface $productMetadata
     * @param Context                  $context
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        Context $context
    ) {
        $this->productMetadata = $productMetadata;

        parent::__construct($context);
    }

    /**
     * @param $data
     *
     * @return bool|false|string
     */
    public function serialize($data)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
            $objManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Framework\Serialize\Serializer\Json $serializer */
            $serializer = $objManager->create('Magento\Framework\Serialize\Serializer\Json');
            return $serializer->serialize($data);
        }

        $result = json_encode($data);
        if (false === $result) {
            throw new \InvalidArgumentException('Unable to serialize value.');
        }
        return $result;
    }

    /**
     * @param $string
     *
     * @return array|bool|float|int|mixed|string|null
     */
    public function unserialize($string)
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
            $objManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Framework\Serialize\Serializer\Json $serializer */
            $serializer = $objManager->create('Magento\Framework\Serialize\Serializer\Json');
            return $serializer->unserialize($string);
        }

        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }
        return $result;
    }
}
