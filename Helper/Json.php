<?php
/**
 * Copyright ©2018 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadataInterface;

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
     * Serialize
     *
     * @param array $data
     *
     * @return string
     */
    public function serialize(array $data): string
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
            /** @var \Magento\Framework\Serialize\Serializer\Json $serializer */
            $serializer = ObjectManager::getInstance()->create(\Magento\Framework\Serialize\Serializer\Json::class);

            return $serializer->serialize($data);
        }

        $result = json_encode($data);
        if (false === $result) {
            throw new \InvalidArgumentException('Unable to serialize value.');
        }

        return $result;
    }

    /**
     * Unserialize
     *
     * @param string $string
     *
     * @return array
     */
    public function unserialize(string $string): array
    {
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
            /** @var \Magento\Framework\Serialize\Serializer\Json $serializer */
            $serializer = ObjectManager::getInstance()->create(\Magento\Framework\Serialize\Serializer\Json::class);

            return $serializer->unserialize($string);
        }

        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }

        return $result;
    }
}
