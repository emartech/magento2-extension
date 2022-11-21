<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Api\Data;

interface AttributeInterface
{
    public const CODE_KEY                = 'code';
    public const NAME_KEY                = 'name';
    public const IS_SYSTEM_KEY           = 'is_system';
    public const IS_VISIBLE_KEY          = 'is_visible';
    public const IS_VISIBLE_ON_FRONT_KEY = 'is_visible_on_front';

    /**
     * GetCode
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * GetName
     *
     * @return string
     */
    public function getName(): string;

    /**
     * GetIsSystem
     *
     * @return bool
     */
    public function getIsSystem(): bool;

    /**
     * GetIsVisible
     *
     * @return bool
     */
    public function getIsVisible(): bool;

    /**
     * GetIsVisibleOnFront
     *
     * @return bool
     */
    public function getIsVisibleOnFront(): bool;

    /**
     * SetCode
     *
     * @param string $code
     *
     * @return \Emartech\Emarsys\Api\Data\AttributeInterface
     */
    public function setCode(string $code): AttributeInterface;

    /**
     * SetName
     *
     * @param string $name
     *
     * @return \Emartech\Emarsys\Api\Data\AttributeInterface
     */
    public function setName(string $name): AttributeInterface;

    /**
     * SetIsSystem
     *
     * @param bool $isSystem
     *
     * @return \Emartech\Emarsys\Api\Data\AttributeInterface
     */
    public function setIsSystem(bool $isSystem): AttributeInterface;

    /**
     * SetIsVisible
     *
     * @param bool $isVisible
     *
     * @return \Emartech\Emarsys\Api\Data\AttributeInterface
     */
    public function setIsVisible(bool $isVisible): AttributeInterface;

    /**
     * SetIsVisibleOnFront
     *
     * @param bool $isVisibleOnFront
     *
     * @return \Emartech\Emarsys\Api\Data\AttributeInterface
     */
    public function setIsVisibleOnFront(bool $isVisibleOnFront): AttributeInterface;
}
