<?php


namespace Emartech\Emarsys\Api;


interface TemplatesApiInterface
{
  /**
   * @return mixed
   */
  public function get();

  /**
   * @param string $temlateId
   * @return mixed
   */
  public function getTemplateVariables($temlateId);
}