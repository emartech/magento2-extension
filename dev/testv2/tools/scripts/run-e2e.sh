#!/usr/bin/env bash

set -e

BASEDIR=$(dirname "$0")
cd "$BASEDIR"

if [[ $VERSION =~ ^2\.4 ]]
then
  composefile="../docker/docker-compose-test-elastic.yml"
else
  composefile="../docker/docker-compose-test.yml"
fi

project_version=$(echo "$VERSION" | tr '.' '_')
echo "\n|--- Using docker compose project $project_version"

docker compose -p mage_e2e_"$project_version" -f $composefile down

echo "\n|--- Running tests on Magento $VERSION"
echo "\n|--- Pulling newest image version"
docker pull registry.itg.cloud/itg-commerce/emarsys-magento2-extension-test/ems-integration-magento-sampledata:"$VERSION"
echo "\n|--- Starting containers"
docker compose -p mage_e2e_"$project_version" -f $composefile up --build -d
echo "\n|--- Waiting for containers to initialize"
sh ./wait.sh e2e >>/dev/null 2>&1

echo "\n|--- Running frontend tests"
docker compose -p mage_e2e_"$project_version" -f $composefile run --rm node sh -c "npm run e2e" --exit-code-from node --abort-on-container-exit node
exitcode=$?
echo "\n\n|--- All tests passed"

docker compose -p mage_e2e_"$project_version" -f $composefile down
exit $exitcode
