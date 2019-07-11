<?php
namespace Emartech\Emarsys\Model\ResourceModel\Event;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Uninstall
 * @package Emartech\Emarsys\Setup
 */
class Collection extends AbstractCollection
{
    // @codingStandardsIgnoreLine
    protected $_idFieldName = 'event_id';
  /**
   * {@inheritdoc}
   */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init(\Emartech\Emarsys\Model\Event::class, \Emartech\Emarsys\Model\ResourceModel\Event::class);
    }
}
