#!/usr/bin/env sh

set -e

BASEDIR=$(dirname "$0")
cd $BASEDIR
docker-compose down

echo "\n|--- Starting containers"
docker-compose up -d
echo "\n|--- Waiting for containers to initialize"
sh ./wait.sh
echo "\n|--- Checking Magento code style"
docker-compose exec -T magento-test bash -c "sh vendor/emartech/emarsys-magento2-extension/dev/codesniffer.sh"
echo "\n|--- Testing Magento DI compilation"
docker-compose exec -T --user application magento-test bash -c "bin/magento setup:di:compile"
echo "\n\n|--- Restarting Magento container"
docker-compose stop magento-test
docker-compose rm -f magento-test
docker-compose up -d
echo "\n|--- Waiting for Magento init"
sh ./wait.sh
echo "\n|--- Running backend tests"
docker-compose run --rm node sh -c "npm t"
echo "\n\n|--- Restarting containers"
docker-compose down
docker-compose up -d
echo "\n|--- Waiting for containers to initialize"
sh ./wait.sh
echo "\n|--- Running frontend tests"
docker-compose run --rm node sh -c "npm run e2e"

echo "\n\n|--- All tests passed"