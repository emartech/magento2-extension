#!/usr/bin/env sh

set -e

BASEDIR=$(dirname "$0")
cd $BASEDIR

alias compose="docker-compose -p mage_$VERSION"

compose down

if [[ "$TABLE_PREFIX" == "" ]]
then
  echo "\n|--- Running tests on Magento $VERSION"
else
  echo "\n|--- Running tests on Magento $VERSION with $TABLE_PREFIX"
fi
echo "\n|--- Starting containers"
compose up -d
echo "\n|--- Waiting for containers to initialize"
sh ./wait.sh >>/dev/null 2>&1
echo "\n|--- Checking Magento code style"
compose exec -T magento-test bash -c "sh vendor/emartech/emarsys-magento2-extension/dev/codesniffer.sh"

echo "\n\n|--- All tests passed"

compose down
