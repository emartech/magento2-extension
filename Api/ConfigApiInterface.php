<?php


namespace Emartech\Emarsys\Api;


use Emartech\Emarsys\Api\Data\ConfigInterface;

interface ConfigApiInterface
{
  /**
   * @param int $websiteId
   * @param ConfigInterface $config
   * @return mixed
   */
  public function set(
      $websiteId,
      ConfigInterface $config
  );
}
