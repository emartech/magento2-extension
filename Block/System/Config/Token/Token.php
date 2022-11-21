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

        return '<textarea disabled>' . $connectToken . '</textarea>';
    }
}
