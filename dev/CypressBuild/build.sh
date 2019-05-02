#!/usr/bin/env sh
BASEDIR=$(dirname "$0")
cd $BASEDIR

echo "Building Cypress $VERSION image..."

if [ ! $1 ]; then
  echo "Usage: build.sh VERSION=[Cypress-version]"
  exit
fi

VERSION=$1

cp package.dist.json package.json

sed -i '' "s/\"version\": CYPRESS_VERSION/\"version\": \"$VERSION\"/g" package.json
sed -i '' "s/\"cypress\": CYPRESS_VERSION/\"cypress\": \"$VERSION\"/g" package.json


DOCKER_BUILDKIT=1 docker build -t emarsys/ems-integration-cypress:$VERSION .

echo "\nPushing to Google Container Registry..."

docker push emarsys/ems-integration-cypress:$VERSION

echo "\nSetting current version as latest"

docker tag emarsys/ems-integration-cypress:$VERSION emarsys/ems-integration-cypress:latest
docker push emarsys/ems-integration-cypress:latest
