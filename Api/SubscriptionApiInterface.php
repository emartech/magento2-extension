<?php

namespace Emartech\Emarsys\Api;

interface SubscriptionApiInterface
{
    /**
     * @param int $page
     * @param int $page_size
     * @param string[] $emails
     * @param bool|null $subscribed
     * @param bool $with_customer
     * @return mixed[]
     */
  public function getList($page = 1, $page_size = 1000, $emails = [], $subscribed = null, $with_customer = false);

  /**
   * @param mixed $subscriptions
   * @return mixed
   */
  public function update($subscriptions);
}
