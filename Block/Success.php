<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */
namespace Emartech\Emarsys\Block;

use Emartech\Emarsys\Helper\Json;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Bundle\Model\Product\Type as BundleProduct;

class Success extends \Magento\Framework\View\Element\Template
{
    // @codingStandardsIgnoreLine
    protected $_checkoutSession;
    // @codingStandardsIgnoreLine
    protected $_orderFactory;
    // @codingStandardsIgnoreLine
    protected $_scopeConfig;

    /**
     * @var OrderItemCollectionFactory
     */
    // @codingStandardsIgnoreLine
    protected $orderItemCollectionFactory;

    /**
     * @var Json
     */
    private $jsonHelper;

    public function __construct(
        Context $context,
        Session $checkoutSession,
        OrderFactory $orderFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        Json $jsonHelper
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
    }

    private function getOrderId()
    {
        return $this->_checkoutSession->getLastOrderId();
    }

    /**
     * @return \Magento\Sales\Model\Order | false
     */
    private function getOrder()
    {
        if ($this->_checkoutSession->getLastRealOrderId()) {
            $order = $this->_orderFactory->create()->load($this->getOrderId());
            return $order;
        }
        return false;
    }

    private function getLineItems()
    {
        $items = [];
        $order = $this->getOrder();
        foreach ($order->getAllItems() as $item) {
            if ($this->notBundleProduct($item) && $this->notConfigurableChild($item)) {
                $qty = (int) $item->getQtyOrdered();
                $sku = $item->getSku();
                $price = $item->getBaseRowTotal() - $item->getBaseDiscountAmount();

                $items[] = [
                    'item' => $sku,
                    'price' => $price,
                    'quantity' => $qty
                ];
            }
        }

        return $items;
    }

    /**
     * @return string
     */
    public function getOrderData()
    {
        return $this->jsonHelper->serialize([
            'orderId' => $this->getOrderId(),
            'items' => $this->getLineItems(),
            'email' => $this->getOrder()->getCustomerEmail()
        ]);
    }

    /**
     * @param $item
     * @return bool
     */
    private function notBundleProduct($item)
    {
        return $item->getProductType() !== BundleProduct::TYPE_CODE;
    }

    /**
     * @param $item
     * @return bool
     */
    private function notConfigurableChild($item)
    {
        return !($item->getProductType() === 'simple'
            && $item->getParentItem() !== null
            && $item->getParentItem()->getProductType() === ConfigurableProduct::TYPE_CODE);
    }
}
