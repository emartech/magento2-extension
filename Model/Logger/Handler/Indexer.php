<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */
declare(strict_types=1);

namespace Emartech\Emarsys\Model\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Indexer extends Base
{
    /**
     * Indexer log file
     */
    const FILENAME = '/var/log/emarsys_delta_indexer.log';

    /**
     * @var string
     */
    protected $fileName = self::FILENAME;

    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;
}
