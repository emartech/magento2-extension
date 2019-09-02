<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\ConfigInterface;

interface ConfigApiInterface
{
    const ATTRIBUTE_CONFIG_POST_TAG = '_attributes';

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

    /**
     * @param string   $type
     * @param string[] $codes
     *
     * @return \Emartech\Emarsys\Api\Data\StatusResponseInterface
     */
    public function setAttributes($type, $codes);
}
