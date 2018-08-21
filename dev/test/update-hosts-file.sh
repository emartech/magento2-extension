#!/usr/bin/env sh

IP=$(getent hosts web | awk '{print $1}')
echo "$IP magento-test.local" >> /etc/hosts
