#!/usr/bin/env bash

set -e

BASEDIR=$(dirname "$0")
cd "$BASEDIR"

if [ -n "$1" ]
then
    echo "Version: $1"
    export VERSION=$1
else
    echo "Default version: 2.4.0ce"
    export VERSION="2.4.0ce"
fi

docker pull registry.itg.cloud/itg-commerce/emarsys-magento2-extension-test/ems-integration-magento-sampledata:$VERSION

docker run --rm \
-v "$(pwd)/../../magento2-extension/:/app/vendor/emartech/emarsys-magento2-extension" \
registry.itg.cloud/itg-commerce/emarsys-magento2-extension-test/ems-integration-magento-sampledata:$VERSION \
bash -c "sh /app/vendor/emartech/emarsys-magento2-extension/dev/codesniffer.sh"
