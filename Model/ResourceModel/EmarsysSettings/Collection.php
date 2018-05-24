<?php
namespace Emartech\Emarsys\Model\ResourceModel\EmarsysSettings;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
  protected $_idFieldName = 'id';
  /**
   * {@inheritdoc}
   */
  protected function _construct()
  {
    $this->_init('Emartech\Emarsys\Model\EmarsysSettings', 'Emartech\Emarsys\Model\ResourceModel\EmarsysSettings');
  }
}