<?php
namespace Emartech\Emarsys\Controller\Adminhtml\HelloWorld;

class Index extends \Magento\Backend\App\Action
{
  /**
  * @var \Magento\Framework\View\Result\PageFactory
  */
  protected $resultPageFactory;

  /**
  * Constructor
  *
  * @param \Magento\Backend\App\Action\Context $context
  * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
  */
  public function __construct(
    \Magento\Backend\App\Action\Context $context,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory
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
