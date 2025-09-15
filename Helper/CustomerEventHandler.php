<?php

namespace Emartech\Emarsys\Helper;

use Emartech\Emarsys\Api\EventRepositoryInterface;
use Emartech\Emarsys\Helper\Customer as CustomerHelper;
use Emartech\Emarsys\Helper\Json as JsonSerializer;
use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Newsletter\Model\Subscriber;
use Magento\Store\Model\StoreManagerInterface;

class CustomerEventHandler extends BaseEventHandler
{
    public const DEFAULT_TYPE = 'customers/update';

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
     * @var RpTokenHelper
     */
    private $tokenHelper;

    /**
     * @param CustomerFactory          $customerFactory
     * @param Subscriber               $subscriber
     * @param ConfigReader             $configReader
     * @param EventFactory             $eventFactory
     * @param EventRepositoryInterface $eventRepository
     * @param EventCollectionFactory   $eventCollectionFactory
     * @param Context                  $context
     * @param StoreManagerInterface    $storeManager
     * @param Json                     $jsonSerializer
     * @param Customer                 $customerHelper
     * @param RpTokenHelper            $tokenHelper
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
        CustomerHelper $customerHelper,
        RpTokenHelper $tokenHelper
    ) {
        $this->customerFactory = $customerFactory;
        $this->subscriber = $subscriber;
        $this->customerHelper = $customerHelper;
        $this->tokenHelper = $tokenHelper;

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
     * Store
     *
     * @param int         $customerId
     * @param int|null    $websiteId
     * @param int|null    $storeId
     * @param string|null $type
     *
     * @return bool
     * @throws AlreadyExistsException
     */
    public function store(int $customerId, ?int $websiteId = null, ?int $storeId = null, ?string $type = null): bool
    {
        if (!$this->isEnabledForWebsite($websiteId)) {
            return false;
        }

        if (!$type) {
            $type = self::DEFAULT_TYPE;
        }

        $customerData = $this->customerHelper->getOneCustomer($customerId, $websiteId, true);

        if (false !== $customerData) {
            $this->saveEvent($websiteId, $storeId, $type, $customerId, $customerData);
        }

        return true;
    }

    /**
     * StoreUserDataDirectly
     *
     * @param array       $customerData
     * @param int         $customerId
     * @param int|null    $websiteId
     * @param int|null    $storeId
     * @param string|null $type
     *
     * @return bool
     * @throws AlreadyExistsException
     */
    public function storeUserDataDirectly(
        array $customerData,
        int $customerId,
        ?int $websiteId = null,
        ?int $storeId = null,
        ?string $type = null
    ): bool {
        if (!$this->isEnabledForWebsite($websiteId)) {
            return false;
        }

        $this->saveEvent($websiteId, $storeId, $type, $customerId, $customerData);

        return true;
    }
}
