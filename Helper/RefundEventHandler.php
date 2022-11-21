<?php

namespace Emartech\Emarsys\Helper;

use Emartech\Emarsys\Api\EventRepositoryInterface;
use Emartech\Emarsys\Helper\Json as JsonSerializer;
use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Item;
use Magento\Store\Model\StoreManagerInterface;

class RefundEventHandler extends BaseEventHandler
{

    /**
     * RefundEventHandler constructor.
     *
     * @param ConfigReader             $configReader
     * @param EventFactory             $eventFactory
     * @param EventRepositoryInterface $eventRepository
     * @param EventCollectionFactory   $eventCollectionFactory
     * @param Context                  $context
     * @param StoreManagerInterface    $storeManager
     * @param JsonSerializer           $jsonSerializer
     */
    public function __construct(
        ConfigReader $configReader,
        EventFactory $eventFactory,
        EventRepositoryInterface $eventRepository,
        EventCollectionFactory $eventCollectionFactory,
        Context $context,
        StoreManagerInterface $storeManager,
        JsonSerializer $jsonSerializer
    ) {
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
     * @param Creditmemo $creditmemo
     *
     * @return bool
     * @throws AlreadyExistsException
     */
    public function store(Creditmemo $creditmemo): bool
    {
        if (!$this->isEnabledForStore($creditmemo->getStoreId())) {
            return false;
        }

        $order = $creditmemo->getOrder();
        $refundData = $creditmemo->getData();
        $refundData['id'] = $creditmemo->getId();
        $refundData['customer_is_guest'] = $order->getCustomerIsGuest();
        $refundData['customer_email'] = $order->getCustomerEmail();

        $refundData['items'] = [];
        $refundData['addresses'] = [];

        foreach ($creditmemo->getAllItems() as $item) {
            $itemArray = $item->toArray();
            $itemArray['product_type'] = $item->getOrderItem()->getProductType();

            if ($parentItem = $this->getParentItem($creditmemo, $item)) {
                $itemArray['parent_item'] = $parentItem;
            }

            $refundData['items'][] = $itemArray;
        }

        if ($creditmemo->getShippingAddress()) {
            $refundData['addresses']['shipping'] = $creditmemo->getShippingAddress()->toArray();
        }

        $refundData['addresses']['billing'] = $creditmemo->getBillingAddress()->toArray();

        $this->saveEvent(
            $creditmemo->getStore()->getWebsiteId(),
            $creditmemo->getStoreId(),
            $this->getRefundEventType($creditmemo->getState()),
            $creditmemo->getId(),
            $refundData
        );

        return true;
    }

    /**
     * GetRefundEventType
     *
     * @param string $state
     *
     * @return string
     */
    private function getRefundEventType(string $state): string
    {
        switch ($state) {
            case Creditmemo::STATE_OPEN:
                return 'refunds/create';
            case Creditmemo::STATE_CANCELED:
                return 'refunds/cancelled';
            case Creditmemo::STATE_REFUNDED:
                return 'refunds/fulfilled';
            default:
                return 'refunds/' . $state;
        }
    }

    /**
     * GetParentItem
     *
     * @param Creditmemo $creditmemo
     * @param Item       $item
     *
     * @return array|null
     */
    private function getParentItem(Creditmemo $creditmemo, Item $item): ?array
    {
        $orderItem = $item->getOrderItem();
        if ($orderItem->getParentItemId()) {
            foreach ($creditmemo->getAllItems() as $creditmemoItem) {
                if ($creditmemoItem->getOrderItemId() == $orderItem->getParentItemId()) {
                    $result = $creditmemoItem->getData();
                    $result['product_type'] = $creditmemoItem->getOrderItem()->getProductType();

                    return $result;
                }
            }
        }

        return null;
    }
}
