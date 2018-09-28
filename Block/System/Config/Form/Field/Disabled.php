<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */

namespace Emartech\Emarsys\Block\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Disabled
 * @package Emartech\Emarsys\Block\System\Config\Form\Field
 */
class Disabled extends \Magento\Config\Block\System\Config\Form\Field
{
    // @codingStandardsIgnoreLine
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setDisabled('disabled');
        return $element->getElementHtml();
    }
}
