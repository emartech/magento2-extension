<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\ConfigInterface;

interface ConfigApiInterface
{
    /**
     * @param int                                        $websiteId
     * @param \Emartech\Emarsys\Api\Data\ConfigInterface $config
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     */
    public function set($websiteId, ConfigInterface $config);

    /**
     * @param int $websiteId
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     */
    public function setDefault($websiteId);
}
