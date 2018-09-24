<?php

namespace Emartech\Emarsys\Helper;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\CustomerFactory;
use Magento\Newsletter\Model\Subscriber;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Emartech\Emarsys\Api\EventRepositoryInterface;

/**
 * Class CustomerEventHandler
 * @package Emartech\Emarsys\Helper
 */
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
     * CustomerEventHandler constructor.
     *
     * @param CustomerFactory          $customerFactory
     * @param Subscriber               $subscriber
     * @param ConfigReader             $configReader
     * @param EventFactory             $eventFactory
     * @param EventRepositoryInterface $eventRepository
     * @param EventCollectionFactory   $eventCollectionFactory
     * @param Context                  $context
     * @param LoggerInterface          $logger
     * @param StoreManagerInterface    $storeManager
     * @param JsonSerializer           $jsonSerializer
     */
    public function __construct(
        CustomerFactory $customerFactory,
        Subscriber $subscriber,
        ConfigReader $configReader,
        EventFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        EventCollectionFactory $eventCollectionFactory,
        Context $context,
        LoggerInterface $logger,
        StoreManagerInterface $storeManager,
        JsonSerializer $jsonSerializer
    ) {
        $this->customerFactory = $customerFactory;
        $this->subscriber = $subscriber;

        parent::__construct(
            $logger,
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

        /** @var Customer $customer */
        $customer = $this->customerFactory->create()->load($customerId);

        $customerData = $customer->toArray();
        $customerData['id'] = $customerData['entity_id'];

        if ($customer->getDefaultBillingAddress()) {
            $customerData['billing_address'] = $customer->getDefaultBillingAddress()->toArray();
        }
        if ($customer->getDefaultShippingAddress()) {
            $customerData['shipping_address'] = $customer->getDefaultShippingAddress()->toArray();
        }

        $subscription = $this->subscriber->loadByCustomerId($customerId);
        $customerData['accepts_marketing'] = $subscription->getStatus();

        $this->saveEvent($websiteId, $storeId, $type, $customer->getId(), $customerData);

        return true;
    }
}
