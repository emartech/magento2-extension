include dev/.env

COMPOSE_FILE=dev/docker-compose.yaml
COMPOSE=docker-compose -f $(COMPOSE_FILE) -p mage


default: help

up: ## Creates containers and starts app
	@$(COMPOSE) up -d --build

down: ## Destorys containers
	@$(COMPOSE) down

start: ## Starts existing containers
	@$(COMPOSE) start

stop: ## Stops containers
	@$(COMPOSE) stop

ps: ## Displays container statuses
	@$(COMPOSE) ps

ssh: ## Enters the web container
	@$(COMPOSE) exec --user application web bash

ssh-root: ## Enters the web container
	@$(COMPOSE) exec web bash

ssh-node: ## Enters the web container
	@$(COMPOSE) run --rm node /bin/sh

exec: ## Runs command in web container (make exec command=your-command)
	@$(COMPOSE) exec web $(command)

install-magento: ## Installs Magento in the container
	@$(COMPOSE) exec web install-magento

install-sampledata: ## Installs Magento sample data in the container
	@$(COMPOSE) exec --user application web bin/magento cache:flush
	@$(COMPOSE) exec web install-sampledata

magento: ## Runs Magento CLI command (make magento command=your-command)
	@$(COMPOSE) exec --user application web bin/magento $(command)

upgrade: ## Runs Magento CLI setup:upgrade command
	@$(COMPOSE) exec --user application web bin/magento cache:flush
	@$(COMPOSE) exec --user application web bin/magento setup:upgrade

compile: ## Runs Magento CLI setup:di:compile command
	@$(COMPOSE) exec --user application web bin/magento cache:flush
	@$(COMPOSE) exec --user application web bin/magento setup:di:compile

flush: ## Runs Magento CLI cache:flush command
	@$(COMPOSE) exec --user application web rm -rf generated/code/
	@$(COMPOSE) exec --user application web bin/magento cache:flush

uninstall: ## Uninstalls the extension from the Magento instance
	@$(COMPOSE) exec db bash -c 'mysql -u root -p${MYSQL_ROOT_PASSWORD} < /opt/uninstall-extension.sql'
	@$(COMPOSE) exec --user application web rm -rf generated/code/Magento/
	@$(COMPOSE) exec --user application web bin/magento cache:flush

mysql: ## Enter MYSQL command line
	@$(COMPOSE) exec db mysql -u magento -p${MYSQL_PASSWORD}

exception: ## Tail Magento exception logs
	@echo "Following var/log/exception.log\n"
	@$(COMPOSE) exec web tail -f -n 10 var/log/exception.log

log: ## Tail Magento exception logs
	@echo "Following var/log/system.log\n"
	@$(COMPOSE) exec web tail -f -n 10 var/log/system.log

create-test-db: ## Creates magento-test database
	@$(COMPOSE) exec db bash -c 'mysql -u root -p${MYSQL_ROOT_PASSWORD} -e "create database if not exists magento_test;"'
	@$(COMPOSE) exec db bash -c 'mysql -u root -p${MYSQL_ROOT_PASSWORD} -e "GRANT ALL PRIVILEGES ON magento_test.* TO \"magento\"@\"%\";"'
	@$(COMPOSE) exec db bash -c 'mysqldump -u root -p${MYSQL_ROOT_PASSWORD} magento > /opt/magento_test.sql'

test: ## Runs tests
	@$(COMPOSE) exec db bash -c 'mysql -u root -p${MYSQL_ROOT_PASSWORD} magento_test < /opt/magento_test.sql'
	@$(COMPOSE) exec web bash -c "sed -i \"s/'dbname' => 'magento'/'dbname' => 'magento_test'/g\" app/etc/env.php"
	-@$(COMPOSE) run --rm node npm t
	@$(COMPOSE) exec web bash -c "sed -i \"s/'dbname' => 'magento_test'/'dbname' => 'magento'/g\" app/etc/env.php"

quick-test: ## Runs tests
	@$(COMPOSE) run --rm node npm t

npm-install: ##
	@$(COMPOSE) run --rm node npm i

help: ## This help message
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' -e 's/:.*#/: #/' | column -t -s '##'