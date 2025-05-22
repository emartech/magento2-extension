<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */

namespace Emartech\Emarsys\Block;

use Emartech\Emarsys\Helper\Json;
use Magento\Bundle\Model\Product\Type as BundleProduct;
use Magento\Checkout\Model\Session;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;

class Success extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Session
     */
    protected $checkoutSession;
    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var OrderItemCollectionFactory
     */
    protected $orderItemCollectionFactory;

    /**
     * @var Json
     */
    private $jsonHelper;

    /**
     * @param Context                    $context
     * @param Session                    $checkoutSession
     * @param OrderFactory               $orderFactory
     * @param OrderItemCollectionFactory $orderItemCollectionFactory
     * @param Json                       $jsonHelper
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        Json $jsonHelper
    ) {
        parent::__construct($context);

        $this->checkoutSession = $checkoutSession;
        $this->orderFactory = $orderFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->scopeConfig = $context->getScopeConfig();
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * GetOrderId
     *
     * @return string|null
     */
    private function getOrderId(): ?string
    {
        return $this->checkoutSession->getLastOrderId();
    }

    /**
     * GetOrder
     *
     * @return Order|null
     */
    private function getOrder(): ?Order
    {
        if ($this->checkoutSession->getLastRealOrderId()) {
            return $this->orderFactory->create()->load($this->getOrderId());
        }

        return null;
    }

    /**
     * GetLineItems
     *
     * @return array
     */
    private function getLineItems(): array
    {
        $items = [];
        $order = $this->getOrder();
        if ($order instanceof Order) {
            foreach ($order->getAllItems() as $item) {
                if ($this->notBundleProduct($item) && $this->notConfigurableChild($item)) {
                    $qty = (int) $item->getQtyOrdered();
                    $sku = $item->getSku();
                    $price = $item->getBaseRowTotalInclTax() - $item->getBaseDiscountAmount();

                    $items[] = [
                        'item'     => $sku,
                        'price'    => $price,
                        'quantity' => $qty
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * GetOrderData
     *
     * @return string
     */
    public function getOrderData(): string
    {
        $email = '';
        $order = $this->getOrder();
        if ($order instanceof Order) {
            $email = $order->getCustomerEmail();
        }

        return $this->jsonHelper->serialize(
            [
                'orderId' => $this->getOrderId(),
                'items'   => $this->getLineItems(),
                'email'   => $email
            ]
        );
    }

    /**
     * NotBundleProduct
     *
     * @param Item $item
     *
     * @return bool
     */
    private function notBundleProduct(Item $item): bool
    {
        return $item->getProductType() !== BundleProduct::TYPE_CODE;
    }

    /**
     * NotConfigurableChild
     *
     * @param Item $item
     *
     * @return bool
     */
    private function notConfigurableChild(Item $item): bool
    {
        return !($item->getProductType() === 'simple'
                 && $item->getParentItem() !== null
                 && $item->getParentItem()->getProductType() === ConfigurableProduct::TYPE_CODE
        );
    }
}
