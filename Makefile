include dev/.env

COMPOSE_FILE=dev/docker-compose.yaml
COMPOSE=docker-compose -f $(COMPOSE_FILE) -p mage


default: help

up: ## Creates containers and starts app
	@$(COMPOSE) up -d

down: ## Destorys containers
	@$(COMPOSE) down

start: ## Starts existing containers
	@$(COMPOSE) start

stop: ## Stops containers
	@$(COMPOSE) stop

ps: ## Displays container statuses
	@$(COMPOSE) ps

ssh: ## Enters the web container
	@$(COMPOSE) exec --user 33 web bash

ssh-root: ## Enters the web container
	@$(COMPOSE) exec web bash

ssh-node: ## Enters the web container
	@$(COMPOSE) run --rm node /bin/sh

exec: ## Runs command in web container (make exec command=your-command)
	@$(COMPOSE) exec web $(command)

install-magento: ## Installs Magento in the container
	@$(COMPOSE) exec web install-magento

install-sampledata: ## Installs Magento sample data in the container
	@$(COMPOSE) exec --user 33 web bin/magento cache:flush
	@$(COMPOSE) exec web install-sampledata

magento: ## Runs Magento CLI command (make magento command=your-command)
	@$(COMPOSE) exec --user 33 web bin/magento $(command)

upgrade: ## Runs Magento CLI setup:upgrade command
	@$(COMPOSE) exec --user 33 web bin/magento cache:flush
	@$(COMPOSE) exec --user 33 web bin/magento setup:upgrade

compile: ## Runs Magento CLI setup:di:compile command
	@$(COMPOSE) exec --user 33 web bin/magento cache:flush
	@$(COMPOSE) exec --user 33 web bin/magento setup:di:compile

flush: ## Runs Magento CLI cache:flush command
	@$(COMPOSE) exec --user 33 web bin/magento cache:flush

mysql: ## Enter MYSQL command line
	@$(COMPOSE) exec db mysql -u magento -p$(MYSQL_PASSWORD)

exception: ## Tail Magento exception logs
	@echo "Following var/log/exception.log\n"
	@$(COMPOSE) exec web tail -f -n 10 var/log/exception.log

test: ## Runs tests
	@$(COMPOSE) run --rm node npm t

npm-install: ##
	@$(COMPOSE) run --rm node npm i && npm i -g gulp

help: ## This help message
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' -e 's/:.*#/: #/' | column -t -s '##'