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

alias compose="docker-compose -p mage_e2e_$VERSION -f $composefile"

compose down

echo "\n|--- Running tests on Magento $VERSION"
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
