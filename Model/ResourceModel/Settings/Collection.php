<?php
namespace Emartech\Emarsys\Model\ResourceModel\Settings;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
  protected $_idFieldName = 'setting_id';
  /**
   * {@inheritdoc}
   */
  protected function _construct()
  {
    $this->_init('Emartech\Emarsys\Model\Settings', 'Emartech\Emarsys\Model\ResourceModel\Settings');
  }
}