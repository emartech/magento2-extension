<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\Data\StatusResponseInterface;

interface ConfigApiInterface
{
    /**
     * @param int                                        $websiteId
     * @param \Emartech\Emarsys\Api\Data\ConfigInterface $config
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     */
    public function set($websiteId, ConfigInterface $config): StatusResponseInterface;

    /**
     * @param int $websiteId
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     */
    public function setDefault($websiteId): StatusResponseInterface;
}
