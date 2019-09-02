<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface AttributeInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface AttributeInterface
{
    const CODE_KEY                = 'code';
    const NAME_KEY                = 'name';
    const IS_SYSTEM_KEY           = 'is_system';
    const IS_VISIBLE_KEY          = 'is_visible';
    const IS_VISIBLE_ON_FRONT_KEY = 'is_visible_on_front';

    /**
     * @return string
     */
    public function getCode();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return bool
     */
    public function getIsSystem();

    /**
     * @return bool
     */
    public function getIsVisible();

    /**
     * @return bool
     */
    public function getIsVisibleOnFront();

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code);

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @param bool $isSystem
     *
     * @return $this
     */
    public function setIsSystem($isSystem);

    /**
     * @param bool $isVisible
     *
     * @return $this
     */
    public function setIsVisible($isVisible);

    /**
     * @param bool $isVisibleOnFront
     *
     * @return $this
     */
    public function setIsVisibleOnFront($isVisibleOnFront);
}
