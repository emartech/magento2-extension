<?php

namespace Emartech\Emarsys\Api;

use Emartech\Emarsys\Api\Data\SystemApiResponseInterface;

interface SystemApiInterface
{
    /**
     * @return \Emartech\Emarsys\Api\Data\SystemApiResponseInterface
     */
    public function get();
}
