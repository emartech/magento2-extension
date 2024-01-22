#!/usr/bin/env sh

set -e

BASEDIR=$(dirname "$0")
cd "$BASEDIR"

docker pull registry.itg.cloud/itg-commerce/emarsys-magento2-extension-test/ems-integration-magento-sampledata:"$VERSION"


docker run --rm \
-v "$(pwd)/../../../../:/app/vendor/emartech/emarsys-magento2-extension" \
registry.itg.cloud/itg-commerce/emarsys-magento2-extension-test/ems-integration-magento-sampledata:$VERSION \
bash -c "sh /app/vendor/emartech/emarsys-magento2-extension/dev/codesniffer.sh"
