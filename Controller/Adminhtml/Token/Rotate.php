<?php

namespace Emartech\Emarsys\Controller\Adminhtml\Token;

use Emartech\Emarsys\Helper\Integration;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Redirect;

class Rotate extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Emartech_Emarsys::emarsys';

    /**
     * @var Integration
     */
    private $integrationHelper;

    /**
     * @param Context     $context
     * @param Integration $integrationHelper
     */
    public function __construct(Context $context, Integration $integrationHelper)
    {
        parent::__construct($context);
        $this->integrationHelper = $integrationHelper;
    }

    /**
     * Execute
     *
     * @return Redirect
     */
    public function execute()
    {
        try {
            $this->integrationHelper->rotateToken();
            $this->messageManager->addSuccessMessage(
                __('A new connect token was generated. The previous token no longer works. Paste the new token into Emarsys.')
            );
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('The connect token could not be rotated.'));
        }

        return $this->resultRedirectFactory->create()
            ->setPath('adminhtml/system_config/edit', ['section' => 'emartech']);
    }
}
