<?php


namespace Emartech\Emarsys\Block\System\Config\Token;

use Emartech\Emarsys\Helper\Integration;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Token extends \Magento\Config\Block\System\Config\Form\Field
{
    private $integrationHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Integration $integrationHelper,
        array $data = []
    ) {
        $this->integrationHelper = $integrationHelper;
        parent::__construct($context, $data);
    }

    // @codingStandardsIgnoreLine
    protected function _getElementHtml(AbstractElement $element)
    {
        $connectToken = $this->integrationHelper->generateConnectToken();

        return '<textarea disabled>' . $connectToken . '</textarea>';
    }
}
