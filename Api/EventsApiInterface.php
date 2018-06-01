<?php


namespace Emartech\Emarsys\Api;


interface EventsApiInterface
{
  /**
   * @param int $since_id
   * @param int $page_size
   * @return mixed
   */
  public function get($since_id, $page_size);
}