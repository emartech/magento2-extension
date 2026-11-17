<?php


namespace Emartech\Emarsys\Block\System\Config\Token;

use Emartech\Emarsys\Helper\Integration;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Oauth\Exception;
use Magento\Setup\Exception as SetupException;

class Token extends Field
{
    /**
     * @var Integration
     */
    private $integrationHelper;

    /**
     * @param Context     $context
     * @param Integration $integrationHelper
     * @param array       $data
     */
    public function __construct(
        Context $context,
        Integration $integrationHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->integrationHelper = $integrationHelper;
    }

    /**
     * GetElementHtml
     *
     * @param AbstractElement $element
     *
     * @return string
     * @throws Exception
     * @throws SetupException
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $connectToken = $this->integrationHelper->generateConnectToken();

        $message = __(
            'The current token stops working immediately. Emarsys sync will fail until the new token is pasted into Emarsys. Continue?'
        );
        $onClick = sprintf(
            'deleteConfirm(%s, %s, {data: {}})',
            json_encode((string) $message),
            json_encode($this->getUrl('emarsys/token/rotate'))
        );

        return '<textarea disabled>' . $connectToken . '</textarea>'
            . '<button type="button" class="action-default" style="margin-top:10px" onclick="'
            . $this->_escaper->escapeHtmlAttr($onClick) . '">'
            . $this->_escaper->escapeHtml(__('Generate new token')) . '</button>';
    }
}
