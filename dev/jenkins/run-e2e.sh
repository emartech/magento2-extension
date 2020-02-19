#!/usr/bin/env sh

set -e

BASEDIR=$(dirname "$0")
cd $BASEDIR

alias compose="docker-compose -p mage_e2e_$VERSION"

compose down

if [ -z "$TABLE_PREFIX" ]
then
  echo "\n|--- Running tests on Magento $VERSION"
else
  echo "\n|--- Running tests on Magento $VERSION with table prefix $TABLE_PREFIX"
fi
echo "\n|--- Pulling newest image version"
docker pull emarsys/ems-integration-magento-sampledata:$VERSION
echo "\n|--- Starting containers"
compose up -d
echo "\n|--- Waiting for containers to initialize"
sh ./wait.sh e2e >>/dev/null 2>&1
echo "\n|--- Running frontend tests"
compose run --rm node sh -c "npm run e2e"

echo "\n\n|--- All tests passed"

compose down
