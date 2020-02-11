<?php

namespace Emartech\Emarsys\Api\Data;

interface ErrorResponseInterface
{
    const ERRORS_KEY   = 'errors';

    /**
     * @return \Emartech\Emarsys\Api\Data\ErrorResponseItemInterface[]|null
     */
    public function getErrors();

    /**
     * @param \Emartech\Emarsys\Api\Data\ErrorResponseItemInterface[] $errors
     *
     * @return $this
     */
    public function setErrors($errors);
}
