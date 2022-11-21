<?php
/**
 * Copyright ©2022 ITG Commerce. All rights reserved.
 * See COPYING.txt for license details.
 *
 * @author Tamás Perencz <tamas.perencz@itgcommerce.com>
 */

namespace Emartech\Emarsys\Helper;

use Magento\Framework\Encryption\EncryptorInterface;

class RpTokenHelper
{
    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
    }

    /**
     * DecryptRpToken
     *
     * @param string $rpToken
     *
     * @return string
     */
    public function decryptRpToken(string $rpToken): string
    {
        if (str_contains($rpToken, ':')) {
            $rpToken = (string)$this->encryptor->decrypt($rpToken);
        }

        return $rpToken;
    }
}
