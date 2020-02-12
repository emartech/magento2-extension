<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ErrorResponseInterface;
use Magento\Framework\DataObject;

class ErrorResponse extends DataObject implements ErrorResponseInterface
{
    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->getData(self::ERRORS_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setErrors($errors)
    {
        $this->setData(self::ERRORS_KEY, $errors);

        return $this;
    }
}
