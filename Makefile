PHPUNIT_VERSION = phpunit-8.5.phar
PHPUNIT_FILENAME = build/$(PHPUNIT_VERSION)
PHPUNIT = php $(PHPUNIT_FILENAME)

vendor:
	@composer install

$(PHPUNIT_FILENAME):
	mkdir -p build
	curl -o $(PHPUNIT_FILENAME) -L https://phar.phpunit.de/$(PHPUNIT_VERSION)

test: test-setup
	@$(PHPUNIT)

test-coverage: test-setup
	@mkdir -p build/coverage
	@XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html build/coverage

test-coveralls: test-setup
	@mkdir -p build/logs
	@XDEBUG_MODE=coverage $(PHPUNIT) --coverage-clover build/logs/clover.xml

test-container-72:
	@docker-compose run --rm app72 sh
	@docker-compose down

test-container-74:
	@docker-compose run --rm app74 sh
	@docker-compose down

test-setup: vendor $(PHPUNIT_FILENAME)
	@rm -f tests/sandbox/*

.PHONY: all test test-container test-coverage test-coveralls test-setup
