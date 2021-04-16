include dev/.env
include tasks/tests.mk

COMPOSE_FILE=dev/docker-compose.yaml
COMPOSE=docker-compose -f $(COMPOSE_FILE) -p mage


default: help

up: ## Creates containers and starts app
	cd ./dev/test && npm i
	@$(COMPOSE) up -d --build
	sh ./dev/wait.sh
	# Creating test db...
	@$(COMPOSE) exec --user root db bash -c 'mysqldump -u root -p${MYSQL_ROOT_PASSWORD} magento_test > /opt/magento_test.sql'

dup: ## Creates containers and starts app
	cd ./dev/test && npm i
	@$(COMPOSE) up --build

down: ## Destorys containers
	@$(COMPOSE) down

restart: down up

start: ## Starts existing containers
	@$(COMPOSE) start

stop: ## Stops containers
	@$(COMPOSE) stop

ps: ## Displays container statuses
	@$(COMPOSE) ps

ssh: ## Enters the web container
	@$(COMPOSE) exec --user application magento-dev bash

ssh-root: ## Enters the web container
	@$(COMPOSE) exec magento-dev bash

ssh-test: ## Enters the web container
	@$(COMPOSE) exec --user application magento-test bash

ssh-node: ## Enters the web container
	@$(COMPOSE) run --rm node /bin/sh

exec: ## Runs command in web container (make exec command=your-command)
	@$(COMPOSE) exec magento-dev $(command)

magento: ## Runs Magento CLI command (make magento command=your-command)
	@$(COMPOSE) exec --user application magento-dev bin/magento $(command)

clear-db: ## Clears magento db
	docker volume rm mage_magento-db

upgrade: ## Runs Magento CLI setup:upgrade command
	@$(COMPOSE) exec --user application magento-dev bin/magento cache:flush
	@$(COMPOSE) exec --user application magento-dev bin/magento setup:upgrade

compile: ## Runs Magento CLI setup:di:compile command
	@$(COMPOSE) exec --user application magento-dev bin/magento cache:flush
	@$(COMPOSE) exec --user application magento-dev bin/magento setup:di:compile

flush: ## Runs Magento CLI cache:flush command
	@$(COMPOSE) exec --user application magento-dev rm -rf generated/code/
	@$(COMPOSE) exec --user application magento-dev bin/magento cache:flush

flush-test: ## Runs Magento CLI cache:flush command
	@$(COMPOSE) exec --user application magento-test rm -rf generated/code/
	@$(COMPOSE) exec --user application magento-test bin/magento cache:flush

uninstall: ## Uninstalls the extension from the Magento instance
	@$(COMPOSE) exec db bash -c 'mysql -u root -p${MYSQL_ROOT_PASSWORD} < /opt/uninstall-extension.sql'
	@$(COMPOSE) exec --user application magento-dev rm -rf generated/code/Magento/
	@$(COMPOSE) exec --user application magento-dev bin/magento cache:flush

mysql: ## Enter MYSQL command line
	@$(COMPOSE) exec db mysql -u magento -p${MYSQL_PASSWORD}

exception: ## Tail Magento exception logs
	@echo "Following var/log/exception.log\n"
	@$(COMPOSE) exec magento-dev tail -f -n 10 var/log/exception.log

log: ## Tail Magento exception logs
	@echo "Following var/log/system.log\n"
	@$(COMPOSE) exec magento-dev tail -f -n 10 var/log/system.log

devlog: ## Tail dev conatiner docker logs
	@$(COMPOSE) logs -f magento-dev

testlog: ## Tail test conatiner docker logs
	@$(COMPOSE) logs -f magento-test

refresh-dev: ## Restart dev conatiner
	@$(COMPOSE) stop magento-dev
	@$(COMPOSE) rm -f magento-dev
	@$(COMPOSE) create magento-dev
	@$(COMPOSE) start magento-dev

refresh-test: ## Restart dev conatiner
	@$(COMPOSE) stop magento-test
	@$(COMPOSE) rm -f magento-test
	@$(COMPOSE) create magento-test
	@$(COMPOSE) start magento-test

build-cypress: ## Build Cypress image (usage: make build-cypress VERSION=3.1.4)
	cd dev/CypressBuild && sh build.sh $(VERSION)

help: ## This help message
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' -e 's/:.*#/: #/' | column -t -s '##'