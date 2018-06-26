<?php
/**
 * @category   Emarsys
 * @package    Emartech_Emarsys
 * @copyright  Copyright (c) 2018 Emarsys. (http://www.emarsys.net/)
 */
namespace Emartech\Emarsys\Block;
use Magento\Framework\View\Element\Template\Context;
/**
 * Class Snippets
 * @package Emartech\Emarsys\Block
 */
class Snippets extends \Magento\Framework\View\Element\Template
{
    /**
     * JavascriptTracking constructor.
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
      Context $context,
      array $data = []
    ) {
      parent::__construct($context, $data);
    }
}