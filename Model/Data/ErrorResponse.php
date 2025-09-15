<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ErrorResponseInterface;
use Emartech\Emarsys\Api\Data\ErrorResponseItemInterface;
use Magento\Framework\DataObject;

class ErrorResponse extends DataObject implements ErrorResponseInterface
{
    /**
     * GetErrors
     *
     * @return ErrorResponseItemInterface[]|null
     */
    public function getErrors(): ?array
    {
        return $this->getData(self::ERRORS_KEY);
    }

    /**
     * SetErrors
     *
     * @param ErrorResponseItemInterface[]|null $errors
     *
     * @return ErrorResponseInterface
     */
    public function setErrors(?array $errors = null): ErrorResponseInterface
    {
        $this->setData(self::ERRORS_KEY, $errors);

        return $this;
    }
}
