<?php

namespace Emartech\Emarsys\Helper;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Newsletter\Model\Subscriber;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Emartech\Emarsys\Helper\Json as JsonSerializer;

use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Emartech\Emarsys\Api\EventRepositoryInterface;
use Emartech\Emarsys\Helper\Customer as CustomerHelper;

class CustomerEventHandler extends BaseEventHandler
{
    const DEFAULT_TYPE = 'customers/update';

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var Subscriber
     */
    private $subscriber;

    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * CustomerEventHandler constructor.
     *
     * @param CustomerFactory          $customerFactory
     * @param Subscriber               $subscriber
     * @param ConfigReader             $configReader
     * @param EventFactory             $eventFactory
     * @param EventRepositoryInterface $eventRepository
     * @param EventCollectionFactory   $eventCollectionFactory
     * @param Context                  $context
     * @param StoreManagerInterface    $storeManager
     * @param JsonSerializer           $jsonSerializer
     * @param CustomerHelper           $customerHelper
     */
    public function __construct(
        CustomerFactory $customerFactory,
        Subscriber $subscriber,
        ConfigReader $configReader,
        EventFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        EventCollectionFactory $eventCollectionFactory,
        Context $context,
        StoreManagerInterface $storeManager,
        JsonSerializer $jsonSerializer,
        CustomerHelper $customerHelper
    ) {
        $this->customerFactory = $customerFactory;
        $this->subscriber = $subscriber;
        $this->customerHelper = $customerHelper;

        parent::__construct(
            $storeManager,
            $configReader,
            $eventFactory,
            $eventRepository,
            $eventCollectionFactory,
            $jsonSerializer,
            $context
        );
    }

    /**
     * @param int         $customerId
     * @param int         $websiteId
     * @param int         $storeId
     * @param null|string $type
     *
     * @return bool
     */
    public function store($customerId, $websiteId, $storeId, $type = null)
    {
        if (!$this->isEnabledForWebsite($websiteId)) {
            return false;
        }

        if (!$type) {
            $type = self::DEFAULT_TYPE;
        }

        $customerData = $this->customerHelper->getOneCustomer($customerId, $websiteId, true);

        if (false !== $customerData) {
            $this->saveEvent(
                $websiteId,
                $storeId,
                $type,
                $customerId,
                $customerData
            );
        }

        return true;
    }

    /**
     * @param array       $customerData
     * @param int         $customerId
     * @param int         $websiteId
     * @param int         $storeId
     * @param null|string $type
     *
     * @return bool
     */
    public function storeUserDataDirectly($customerData, $customerId, $websiteId, $storeId, $type = null)
    {
        if (!$this->isEnabledForWebsite($websiteId)) {
            return false;
        }

        $this->saveEvent(
            $websiteId,
            $storeId,
            $type,
            $customerId,
            $customerData
        );

        return true;
    }
}
