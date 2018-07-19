<?php


namespace Emartech\Emarsys\Model;


use Emartech\Emarsys\Api\Data\EventInterface;
use Magento\Framework\Model\AbstractModel;

class Event extends AbstractModel implements EventInterface
{
  /**
   * Define resource model
   */
  protected function _construct()
  {
    $this->_init('Emartech\Emarsys\Model\ResourceModel\Event');
  }
}