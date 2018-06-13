<?php


namespace Emartech\Emarsys\Api;


interface ProductsApiInterface
{
  /**
   * @param int $page
   * @param int $page_size
   * @return mixed
   */
  public function get($page, $page_size);
}