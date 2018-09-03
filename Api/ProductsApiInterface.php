<?php


namespace Emartech\Emarsys\Api;


interface ProductsApiInterface
{
  /**
   * @param int $page
   * @param int $page_size
   * @return string
   */
  public function get($page, $page_size);
}