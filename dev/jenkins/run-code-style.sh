#!/usr/bin/env sh

set -e

BASEDIR=$(dirname "$0")
cd $BASEDIR

docker pull emarsys/ems-integration-magento-sampledata:$VERSION

docker run --rm \
-v "$(pwd)/../../:/app/vendor/emartech/emarsys-magento2-extension" \
emarsys/ems-integration-magento-sampledata:$VERSION \
bash -c "sh /app/vendor/emartech/emarsys-magento2-extension/dev/codesniffer.sh"
