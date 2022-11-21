#!/usr/bin/env bash

sniff() {
  /app/vendor/bin/phpcs --colors \
    --standard=Magento2 \
    --ignore=*/vendor/emartech/emarsys-magento2-extension/vendor/*,*/vendor/emartech/emarsys-magento2-extension/dev/*,*/vendor/emartech/emarsys-magento2-extension/.idea/* \
    /app/vendor/emartech/emarsys-magento2-extension/ \
    --extensions=php,phtml
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