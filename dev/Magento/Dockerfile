FROM alexcheng/magento2:2.2.3

COPY *.json /var/www/html/
COPY setup /etc/my_init.d/
COPY docker-healthcheck /usr/local/bin/

WORKDIR /var/www/html

HEALTHCHECK CMD ["docker-healthcheck"]



