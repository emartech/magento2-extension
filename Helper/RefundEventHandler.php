<?php

namespace Emartech\Emarsys\Helper;

use Emartech\Emarsys\Api\EventRepositoryInterface;
use Emartech\Emarsys\Helper\Json as JsonSerializer;
use Emartech\Emarsys\Model\EventFactory;
use Emartech\Emarsys\Model\ResourceModel\Event\CollectionFactory as EventCollectionFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\Order\Creditmemo;
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
     * @param Creditmemo $creditmemo
     *
     * @return bool
     */
    public function store(Creditmemo $creditmemo)
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
     * @param string $state
     *
     * @return string
     */
    private function getRefundEventType($state)
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
     * @param Creditmemo $creditmemo
     * @param Creditmemo\Item $item
     * @return bool|array
     */
    private function getParentItem($creditmemo, $item)
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
        return false;
    }
}
