#!/usr/bin/env bash

echo "-|| Setting up secrets"
cd /app
rm -f auth.json
cp /opt/emartech/auth.json.template auth.json

sed -i "s/MAGENTO_REPO_KEY/$MAGENTO_REPO_KEY/g" auth.json
sed -i "s/MAGENTO_REPO_SECRET/$MAGENTO_REPO_SECRET/g" auth.json
sed -i "s/GITHUB_TOKEN/$GITHUB_TOKEN/g" auth.json

echo "-|| Cleaning up filesystem"
rm -rf /app/generated/code/
rm -rf /app/var/cache/

echo "-|| Checking file permissions"
chown -R application:application /home/application/
chown -R application:application /app/generated/
chown -R application:application /app/var/
chown -R application:application /app/pub/
chown -R application:application /app/*.json

echo "-|| Checking database..."
if [ ! $(mysql -h $MYSQL_HOST -u root -p$MYSQL_ROOT_PASSWORD $MYSQL_DATABASE -e 'show tables;' 2> /dev/null | grep core_config) ]
then
  echo "   >> Injecting database..."
    /opt/emartech/inject-db
else
  echo "   >> Database exists, skipping injection."
fi

echo "-|| Starting setup script..."
su application /opt/emartech/setup

touch /setup-ready

echo "-|| Setup ready!"
echo "-|| Starting up compilation..."

/app/bin/magento setup:di:compile