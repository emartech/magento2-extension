<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Helper;

use Exception;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\ObjectManagerInterface;

class LinkField extends AbstractHelper
{

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * LinkField constructor.
     *
     * @param Context                $context
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager
    ) {
        parent::__construct($context);
        $this->objectManager = $objectManager;
    }

    /**
     * GetEntityLinkField
     *
     * @param string $class
     * @param string $linkField
     *
     * @return string
     * @throws Exception
     */
    public function getEntityLinkField(string $class, string $linkField = 'entity_id'): string
    {
        if (class_exists(MetadataPool::class)) {
            $metadataPool = $this->objectManager->create(MetadataPool::class);
            $linkField = $metadataPool->getMetadata($class)->getLinkField();
        }

        return $linkField;
    }
}
