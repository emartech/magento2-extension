#!/usr/bin/env sh

set -e

BASEDIR=$(dirname "$0")
cd $BASEDIR

if [[ $VERSION =~ ^2\.4 ]]
then
  composefile="docker-compose-elastic.yml"
else
  composefile="docker-compose.yml"
fi

alias compose="docker-compose -p mage_unit_$VERSION -f $composefile"

compose down

echo "\n|--- Running tests on Magento $VERSION"
echo "\n|--- Pulling newest image version"
docker pull emarsys/ems-integration-magento-sampledata:$VERSION
echo "\n|--- Starting containers"
compose up -d
echo "\n|--- Waiting for containers to initialize"
sh ./wait.sh unit >>/dev/null 2>&1

if ![[ $VERSION =~ ^2\.3\.3 ]]
then
  echo "\n|--- Testing Magento DI compilation"
  compose exec -T --user application magento-test bash -c "bin/magento setup:di:compile"
fi

echo "\n|--- Running backend tests"
compose run --rm node sh -c "npm run mocha"
echo "\n\n|--- Stopping containers"
compose down
