<?php

namespace Emartech\Emarsys\Plugin;

use Emartech\Emarsys\Helper\EmailEventHandler;
use \Magento\Email\Model\Template;
use Emartech\Emarsys\Model\SettingsFactory;
use \Psr\Log\LoggerInterface;

class Email
{
  protected $logger;

  protected $ignoredTemplates = [
    'template_styles',
    'design_email_header_template',
    'design_email_footer_template'
  ];

  /**
   * @var EmailEventHandler
   */
  private $emailEventHandler;

  public function __construct(
    EmailEventHandler $emailEventHandler,
    LoggerInterface $logger
  ) {
    $this->logger = $logger;
    $this->emailEventHandler = $emailEventHandler;
  }

  public function afterGetProcessedTemplate(Template $subject, $result, ...$args)
  {
    $this->logger->info('3');

    if (in_array($subject->getId(), $this->ignoredTemplates)) {
      return $result;
    }
    $this->logger->info('4');

    $this->emailEventHandler->store($subject->getId(), $args);

    return $result;
  }
}
