<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */
namespace Emartech\Emarsys\Block;

use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProduct;
use Magento\Bundle\Model\Product\Type as BundleProduct;

/**
 * Class Success
 * @package Emartech\Emarsys\Block
 */
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

    // @codingStandardsIgnoreStart
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context);
    }
    // @codingStandardsIgnoreEnd

    private function getOrderId()
    {
        return $this->_checkoutSession->getLastOrderId();
    }

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
                $price = $item->getBasePrice() - $item->getBaseDiscountAmount();

                $items[] = [
                    'item' => $sku,
                    'price' => $price,
                    'quantity' => $qty
                ];
            }
        }

        return $items;
    }

    public function getOrderData()
    {
        return [
            'orderId' => $this->getOrderId(),
            'items' => $this->getLineItems()
        ];
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
