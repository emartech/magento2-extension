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
echo "\n|--- Testing Magento DI compilation"
compose exec -T --user application magento-test bash -c "bin/magento setup:di:compile"
echo "\n|--- Running backend tests"
compose run --rm node sh -c "npm t"
echo "\n\n|--- Restarting containers"
compose down
compose up -d
echo "\n|--- Waiting for containers to initialize"
sh ./wait.sh >>/dev/null 2>&1
echo "\n|--- Running frontend tests"
compose run --rm node sh -c "npm run e2e"

echo "\n\n|--- All tests passed"

compose down
