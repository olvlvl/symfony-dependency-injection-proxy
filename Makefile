PHPUNIT=$(shell which phpunit)

vendor:
	@composer install

test: test-setup
	@php -d xdebug.coverage_enable=0 $(PHPUNIT)

test-coverage: test-setup
	@mkdir -p build/coverage
	@$(PHPUNIT) --coverage-html build/coverage

test-coveralls: test-setup
	@mkdir -p build/logs
	composer require satooshi/php-coveralls '^2.0'
	@$(PHPUNIT) --coverage-clover build/logs/clover.xml
	php vendor/bin/php-coveralls -v

test-container:
	@docker-compose run --rm app sh
	@docker-compose down

test-setup: vendor
	@rm -f tests/sandbox/*

.PHONY: all test test-container test-coverage test-coveralls test-setup
