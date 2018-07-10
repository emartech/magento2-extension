<?php

namespace Emartech\Emarsys\Api;

interface SubscriptionApiInterface
{
  /**
   * @param int $page
   * @param int $page_size
   * @param string[] $emails
   * @return mixed[]
   */
  public function getList($page, $page_size, $emails = []);
}
