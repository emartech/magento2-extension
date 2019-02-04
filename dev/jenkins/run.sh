#!/usr/bin/env sh

set -e

BASEDIR=$(dirname "$0")
cd $BASEDIR

alias compose="docker-compose -p mage_$VERSION"

compose down

compose build node --build-arg http_proxy=$http_proxy --build-arg https_proxy=$https_proxy

echo "\n|--- Running tests on Magento $VERSION"
echo "\n|--- Starting containers"
compose up -d
echo "\n|--- Waiting for containers to initialize"
sh ./wait.sh
echo "\n|--- Checking Magento code style"
compose exec -T magento-test bash -c "sh vendor/emartech/emarsys-magento2-extension/dev/codesniffer.sh"
echo "\n|--- Testing Magento DI compilation"
compose exec -T --user application magento-test bash -c "bin/magento setup:di:compile"
echo "\n\n|--- Restarting Magento container"
compose stop magento-test
compose rm -f magento-test
compose up -d
echo "\n|--- Waiting for Magento init"
sh ./wait.sh
echo "\n|--- Running backend tests"
compose run --rm node sh -c "npm t"
echo "\n\n|--- Restarting containers"
compose down
compose up -d
echo "\n|--- Waiting for containers to initialize"
sh ./wait.sh
echo "\n|--- Running frontend tests"
compose run --rm node sh -c "npm run e2e"

echo "\n\n|--- All tests passed"

compose down
