<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */
namespace Emartech\Emarsys\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Snippets
 * @package Emartech\Emarsys\Block
 */
class Snippets extends \Magento\Framework\View\Element\Template
{
    /**
     * @var CustomerSession
     */
    protected $customerSession;

    /**
     * Snippets constructor.
     *
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
      Context $context,
      CustomerSession $customerSession,
      array $data = []
    ) {
      $this->customerSession = $customerSession;
      parent::__construct($context, $data);
    }

    /**
     * Get Customer Id
     *
     * @return bool|string
     */
    public function getCustomerId()
    {
      try {
        if ($this->customerSession->isLoggedIn()) {
          $customer = $this->customerSession->getCustomer();
          return $customer->getEntityId();
        }
      } catch (\Exception $e) {
          throw $e;
      }

      return false;
    }
}