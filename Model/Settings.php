<?php

namespace Emartech\Emarsys\Model;

use Emartech\Emarsys\Api\Data\SettingsInterface;
use Magento\Framework\Model\AbstractModel;

class Settings extends AbstractModel
{
  /**
   * Define resource model
   */
  protected function _construct()
  {
    $this->_init('Emartech\Emarsys\Model\ResourceModel\Settings');
  }
}