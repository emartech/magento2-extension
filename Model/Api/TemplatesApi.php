<?php


namespace Emartech\Emarsys\Model\Api;


use Emartech\Emarsys\Api\TemplatesApiInterface;
use Magento\Email\Model\BackendTemplate;
use Magento\Email\Model\Template;
use Magento\Email\Model\Template\Config;
use Magento\Framework\ObjectManagerInterface;

class TemplatesApi implements TemplatesApiInterface
{

  /** @var Config */
  protected $emailConfig;
  /** @var ObjectManagerInterface */
  private $objectManager;

  public function __construct(
    Config $emailConfig,
    ObjectManagerInterface $objectManager
  )
  {
    $this->emailConfig = $emailConfig;
    $this->objectManager = $objectManager;
  }

  public function get()
  {
    $templates = [];

    foreach ($this->emailConfig->getAvailableTemplates() as $template) {
      $templateId = $template['value'];

      /** @var Template $template */
      $template = $this->objectManager->create(BackendTemplate::class);

      $parts = $this->emailConfig->parseTemplateIdParts($templateId);
      $templateId = $parts['templateId'];
      $theme = $parts['theme'];

      if ($theme) {
        $template->setForcedTheme($templateId, $theme);
      }
      $template->setForcedArea($templateId);

      $template->loadDefault($templateId);
      $template->setData('orig_template_code', $templateId);
      $template->setData(
        'template_variables',
        $template->getVariablesOptionArray()
      );

      $templates[] = [
        'template' => $templateId,
        'variables' => json_decode($template->getOrigTemplateVariables())
      ];
    }
    return $templates;

  }

  /**
   * @param string $temlateId
   * @return mixed
   */
  public function getTemplateVariables($temlateId)
  {
    // TODO: Implement getTemplateVariables() method.
  }
}