#!/usr/bin/env sh

set -e

BASEDIR=$(dirname "$0")
cd $BASEDIR

alias compose="docker-compose -p mage_$VERSION"

compose down

echo "\n|--- Checking Magento code style"
compose run -T magento-test bash -c "sh vendor/emartech/emarsys-magento2-extension/dev/codesniffer.sh"

compose down
