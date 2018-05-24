<?php

namespace Emartech\Emarsys\Model;

use Magento\Framework\Model\AbstractModel;

class EmarsysSettings extends AbstractModel
{
  /**
   * Define resource model
   */
  protected function _construct()
  {
    $this->_init('Emartech\Emarsys\Model\ResourceModel\EmarsysSettings');
  }
}