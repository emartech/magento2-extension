<?php
namespace Emartech\Emarsys\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Settings extends AbstractDb
{
  /**
   * construct
   * @return void
   */
  protected function _construct()
  {
    $this->_init('emarsys_settings', 'setting_id');
  }
}