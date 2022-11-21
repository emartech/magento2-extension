<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\SystemApiResponseInterface;

interface SystemApiInterface
{
    /**
     * Get
     *
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function get(): SystemApiResponseInterface;
}
