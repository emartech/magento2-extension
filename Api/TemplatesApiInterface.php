<?php


namespace Emartech\Emarsys\Api;


interface TemplatesApiInterface
{
  /**
   * @return string
   */
  public function get();

  /**
   * @param string $temlateId
   * @return string
   */
  public function getTemplateVariables($temlateId);
}