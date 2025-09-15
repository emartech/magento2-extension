<?php

namespace Emartech\Emarsys\Api\Data;

interface ErrorResponseInterface
{
    public const ERRORS_KEY = 'errors';

    /**
     * GetErrors
     *
     * @return \Emartech\Emarsys\Api\Data\ErrorResponseItemInterface[]|null
     */
    public function getErrors(): ?array;

    /**
     * SetErrors
     *
     * @param \Emartech\Emarsys\Api\Data\ErrorResponseItemInterface[]|null $errors
     *
     * @return \Emartech\Emarsys\Api\Data\ErrorResponseInterface
     */
    public function setErrors(?array $errors = null): ErrorResponseInterface;
}
