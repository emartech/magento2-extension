COMPOSE=docker-compose -f $(COMPOSE_FILE) -p mage

test: mocha run-e2e

test-code-style:
	-@$(COMPOSE) run --rm node npm run code-style

mocha: reset-test-db flush-test run-npmt

run-e2e: reset-test-db flush-test run-docker-e2e

run-e2e-debug: reset-test-db run-docker-e2e

open-e2e: reset-test-db set-local-baseurl flush-test open-local-e2e set-docker-baseurl flush-test

run-e2e-local: reset-test-db set-local-baseurl flush-test run-local-e2e set-docker-baseurl flush-test

create-test-db: ## Creates magento-test database
	@$(COMPOSE) exec db bash -c 'mysqldump -u root -p${MYSQL_ROOT_PASSWORD} magento_test > /opt/magento_test.sql'

reset-test-db:
	@$(COMPOSE) exec db bash -c 'mysql -u root -p${MYSQL_ROOT_PASSWORD} magento_test < /opt/magento_test.sql'

run-docker-e2e:
	-@$(COMPOSE) run --rm node npm run e2e

run-docker-e2e-debug:
	-@$(COMPOSE) run --rm node npm run e2e:debug

open-local-e2e:
	-CYPRESS_baseUrl=http://magento-test.local:8889 ./dev/test/node_modules/.bin/cypress open --project ./dev/test/

run-local-e2e:
	-CYPRESS_baseUrl=http://magento-test.local:8889 ./dev/test/node_modules/.bin/cypress run --project ./dev/test/

run-npmt:
	-@$(COMPOSE) run --rm node npm t

quick-test: ## Runs tests
	-@$(COMPOSE) run --rm -e "QUICK_TEST=true" node npm run quick-test

quick-e2e: ## Runs tests
	-@$(COMPOSE) run --rm -e "QUICK_TEST=true" node npm run e2e

set-local-baseurl:
	@$(COMPOSE) exec --user application magento-test bash -c "bin/magento config:set web/unsecure/base_url http://magento-test.local:8889/"

set-docker-baseurl:
	@$(COMPOSE) exec --user application magento-test bash -c "bin/magento config:set web/unsecure/base_url http://magento-test.local/"

codesniffer:
	@$(COMPOSE) exec --user application magento-dev bash -c "sh vendor/emartech/emarsys-magento2-extension/dev/codesniffer.sh"
