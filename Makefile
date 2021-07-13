PHPUNIT = vendor/bin/phpunit

vendor:
	@composer install

.PHONY: test
test: test-setup
	@$(PHPUNIT)

.PHONY: test-coverage
test-coverage: test-setup
	@mkdir -p build/coverage
	@XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html build/coverage

.PHONY: test-coveralls
test-coveralls: test-setup
	@mkdir -p build/logs
	@XDEBUG_MODE=coverage $(PHPUNIT) --coverage-clover build/logs/clover.xml

.PHONY: test-container-72
test-container-72:
	@docker-compose run --rm app72 sh
	@docker-compose down

.PHONY: test-container-74
test-container-74:
	@docker-compose run --rm app74 sh
	@docker-compose down

.PHONY: test-container-80
test-container-80:
	@docker-compose run --rm app80 sh
	@docker-compose down

.PHONY: test-setup
test-setup: vendor $(PHPUNIT_FILENAME)
	@rm -f tests/sandbox/*

.PHONY: lint
lint:
	@phpcs
	@vendor/bin/phpstan
