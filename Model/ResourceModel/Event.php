<?php
namespace Emartech\Emarsys\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Event extends AbstractDb
{
  /**
   * construct
   * @return void
   */
  protected function _construct()
  {
    $this->_init('emarsys_events', 'event_id');
  }
}