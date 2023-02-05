PHPUNIT = vendor/bin/phpunit

vendor:
	@composer install

.PHONY: test-dependencies
test-dependencies: vendor test-cleanup

.PHONY: test
test: test-dependencies
	@$(PHPUNIT)

.PHONY: test-coverage
test-coverage: test-dependencies
	@mkdir -p build/coverage
	@XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html build/coverage

.PHONY: test-coveralls
test-coveralls: test-dependencies
	@mkdir -p build/logs
	@XDEBUG_MODE=coverage $(PHPUNIT) --coverage-clover build/logs/clover.xml

.PHONY: test-cleanup
test-cleanup:
	@rm -rf tests/sandbox/*

.PHONY: test-container
test-container: test-container-80

.PHONY: test-container-80
test-container-80:
	@-docker-compose run --rm app80 bash
	@docker-compose down -v

.PHONY: test-container-81
test-container-81:
	@-docker-compose run --rm app81 bash
	@docker-compose down -v

.PHONY: test-container-82
test-container-82:
	@-docker-compose run --rm app81 bash
	@docker-compose down -v

.PHONY: lint
lint:
	@XDEBUG_MODE=off phpcs -s
	@XDEBUG_MODE=off vendor/bin/phpstan
