<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */
namespace Emartech\Emarsys\Block;

use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;

/**
 * Class Success
 * @package Emartech\Emarsys\Block
 */
class Success extends \Magento\Framework\View\Element\Template
{
    protected $_checkoutSession;
    protected $_orderFactory;
    protected $_scopeConfig;

    /**
     * @var OrderItemCollectionFactory
     */
    protected $orderItemCollectionFactory;

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
        foreach ($order->getAllVisibleItems() as $item) {
            $qty = intval($item->getQtyOrdered());
            $product = $this->getLoadProduct($item->getProductId());
            $sku = $item->getSku();
            if (($item->getProductType() == \Magento\Bundle\Model\Product\Type::TYPE_CODE) && (!$product->getPriceType())) {
                $collection = $this->orderItemCollectionFactory->create()
                    ->addAttributeToFilter('parent_item_id', ['eq' => $item['item_id']])
                    ->load();
                $bundleBaseDiscount = 0;
                $bundleDiscount = 0;
                foreach ($collection as $collPrice) {
                    $bundleBaseDiscount += $collPrice['base_discount_amount'];
                    $bundleDiscount += $collPrice['discount_amount'];
                }
                $price = $item->getBaseRowTotal() - $bundleBaseDiscount;
            } else {
                $price = $item->getBaseRowTotal() - $item->getBaseDiscountAmount();
            }

            $items[] = [
                'item' => $sku,
                'price' => $price,
                'quantity' => $qty
            ];
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
}
