<?php
namespace Emartech\Emarsys\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Event extends AbstractDb
{
  /**
   * construct
   * @return void
   */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('emartech_events_data', 'event_id');
    }
}
