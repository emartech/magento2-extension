<?php

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Emartech_Emarsys',
    isset($file) ? dirname($file) : __DIR__
);
