<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\Data\StatusResponseInterface;

interface ConfigApiInterface
{
    /**
     * Set
     *
     * @param int                                        $websiteId
     * @param \Emartech\Emarsys\Api\Data\ConfigInterface $config
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     */
    public function set(int $websiteId, ConfigInterface $config): StatusResponseInterface;

    /**
     * SetAttributes
     *
     * @param string   $type
     * @param int      $websiteId
     * @param string[] $codes
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     */
    public function setAttributes(string $type, int $websiteId, array $codes): StatusResponseInterface;
}
