<?php
namespace Emartech\Emarsys\Controller\Adminhtml\Integrate;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
  /** @var PageFactory */
  protected $resultPageFactory;

  /**
   * Constructor
   *
   * @param Context $context
   * @param PageFactory $resultPageFactory
   */
  public function __construct(
    Context $context,
    PageFactory $resultPageFactory
  ) {
    parent::__construct($context);
    $this->resultPageFactory = $resultPageFactory;
  }

  /**
   * @return \Magento\Framework\View\Result\Page
   */
  public function execute()
  {
    $resultPage = $resultPage = $this->resultPageFactory->create();
    $resultPage->setActiveMenu('Emarsys_Extension::parent');
    return $resultPage;
  }
}
