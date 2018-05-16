<?php
namespace Emartech\Emarsys\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Emartech\Emarsys\Helper\Integration;

class Integrate extends Template
{
  /**
   * @var Integration
   */
  private $integration;

  /**
   * @param Template\Context $context
   * @param Integration $integration
   * @param array $data
   */
  public function __construct(
    Template\Context $context,
    Integration $integration,
    array $data = []
  ) {
    $this->integration = $integration;
    parent::__construct($context, $data);
  }

  /**
   * @return string
   */
  public function getToken()
  {
    return $this->integration->getConnectToken();
  }
}