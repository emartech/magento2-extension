<?php
namespace Emartech\Emarsys\Model\ResourceModel\Event;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Emartech\Emarsys\Model\Event as Model;
use Emartech\Emarsys\Model\ResourceModel\Event as ResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'event_id';

    /**
     * _construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
