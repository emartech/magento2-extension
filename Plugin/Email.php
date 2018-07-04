<?php

namespace Emartech\Emarsys\Plugin;

class Email
{
    public function afterGetProcessedTemplate(\Magento\Email\Model\Template $subject, $result, ...$args)
    {
        return $result;
    }
}
