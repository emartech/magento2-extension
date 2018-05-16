FROM webdevops/php-apache-dev:alpine

COPY *.json /app/
WORKDIR /app
RUN su application \
  && composer install \
  && chmod +x bin/magento \
  && bin/magento setup:upgrade



