.PHONY: validate install update deps phpcs-check phpcs-fix phpmin-compatibility phpmax-compatibility phpstan analyze tests testdox ci clean

PHP_VERSION_MIN := 8.3
PHP_VERSION_MAX := 8.4
define header =
    @if [ -t 1 ]; then printf "\n\e[37m\e[100m  \e[104m $(1) \e[0m\n"; else printf "\n### $(1)\n"; fi
endef

#~ Composer dependency
validate:
	$(call header,Composer Validation)
	@composer validate

install:
	$(call header,Composer Install)
	@composer install

update:
	$(call header,Composer Update)
	@composer update
	@composer bump --dev-only

composer.lock: install

#~ Vendor binaries dependencies
vendor/bin/php-cs-fixer:
vendor/bin/phpstan:
vendor/bin/phpunit:
vendor/bin/phpcov:

#~ Report directories dependencies
build/reports/phpunit:
	@mkdir -p build/reports/phpunit

build/reports/phpcs:
	@mkdir -p build/reports/cs

build/reports/phpstan:
	@mkdir -p build/reports/phpstan

#~ main commands
deps: composer.json
	$(call header,Checking Dependencies)
	@DEBUG_MODE=off ./vendor/bin/composer-dependency-analyser --config=./ci/composer-dependency-analyser.php # for shadow, unused required dependencies & missing ext-*

phpcs-check: vendor/bin/php-cs-fixer
	$(call header,Checking Code Style)
	@./vendor/bin/php-cs-fixer check

phpcs-fix: vendor/bin/php-cs-fixer
	$(call header,Fixing Code Style)
	@./vendor/bin/php-cs-fixer fix -v

phpcs: vendor/bin/php-cs-fixer build/reports/phpcs
	$(call header,Checking Code Style)
	@./vendor/bin/php-cs-fixer check --format=checkstyle > ./build/reports/cs/phpcs.xml

phpmin-compatibility: vendor/bin/phpstan build/reports/phpstan
	$(call header,Checking PHP ${PHP_VERSION_MIN} compatibility)
	@XDEBUG_MODE=off ./vendor/bin/phpstan analyse --configuration=./ci/phpmin-compatibility.neon --error-format=checkstyle > ./build/reports/phpstan/phpmin-compatibility.xml

phpmax-compatibility: vendor/bin/phpstan build/reports/phpstan
	$(call header,Checking PHP ${PHP_VERSION_MAX} compatibility)
	@XDEBUG_MODE=off ./vendor/bin/phpstan analyse --configuration=./ci/phpmax-compatibility.neon --error-format=checkstyle > ./build/reports/phpstan/phpmax-compatibility.xml

phpstan: vendor/bin/phpstan build/reports/phpstan
	$(call header,Running Static Analyze)
	@XDEBUG_MODE=off ./vendor/bin/phpstan analyse --error-format=checkstyle > ./build/reports/phpstan/phpstan.xml

analyze: vendor/bin/phpstan build/reports/phpstan
	$(call header,Running Static Analyze - Pretty tty format)
	@XDEBUG_MODE=off ./vendor/bin/phpstan analyse --error-format=table

tests: vendor/bin/phpunit build/reports/phpunit #ci
	$(call header,Running Unit Tests)
	@XDEBUG_MODE=coverage php ./vendor/bin/phpunit --coverage-clover=./build/reports/phpunit/clover.xml --log-junit=./build/reports/phpunit/unit.xml --coverage-php=./build/reports/phpunit/unit.cov --coverage-html=./build/reports/coverage/ --fail-on-warning

testdox: vendor/bin/phpunit #manual
	$(call header,Running Unit Tests (Pretty format))
	@XDEBUG_MODE=coverage php ./vendor/bin/phpunit --testsuite=unit --fail-on-warning --testdox

clean:
	$(call header,Cleaning previous build)
	@if [ "$(shell ls -A ./build)" ]; then rm -rf ./build/*; fi; echo " done"

ci: clean validate install deps phpcs-check tests phpmin-compatibility phpmax-compatibility analyze
