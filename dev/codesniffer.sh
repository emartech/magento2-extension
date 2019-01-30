#!/usr/bin/env bash

sniff() {
  /app/vendor/bin/phpcs --colors \
    --standard=MEQP2 \
    --ignore=*/vendor/emartech/emarsys-magento2-extension/vendor/*,*/vendor/emartech/emarsys-magento2-extension/dev/* \
    /app/vendor/emartech/emarsys-magento2-extension/ \
    --extensions=php
}

echo "\nRunning sniffer..."

REPORT=$(sniff)

if [ "$REPORT" ]
then
  echo "$REPORT"
  echo "\n\e[31mFAILED\e[39m\n"
  exit 1
else
  echo "\n\e[32mSUCCESS"
  echo "No style errors found.\e[39m\n"
fi