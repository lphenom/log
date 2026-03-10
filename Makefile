DC = docker compose
PHP = $(DC) run --rm php

.PHONY: up down build shell install test lint lint-fix phpstan phpcs kphp-check

## Start containers
up:
	$(DC) up -d

## Stop containers
down:
	$(DC) down

## Build / rebuild images
build:
	$(DC) build

## Open bash shell in php container
shell:
	$(DC) run --rm php bash

## Install Composer dependencies (runs inside Docker)
install:
	$(PHP) composer install --no-interaction --prefer-dist --optimize-autoloader

## Run PHPUnit tests
test:
	$(PHP) composer test

## Run PHP CS Fixer (dry-run)
lint:
	$(PHP) composer lint

## Run PHP CS Fixer (auto-fix)
lint-fix:
	$(PHP) composer lint:fix

## Run PHPStan
phpstan:
	$(PHP) composer phpstan

## Run PHPCS
phpcs:
	$(PHP) composer phpcs

## Build KPHP binary + PHAR and verify both stages
kphp-check:
	docker build -f Dockerfile.check -t lphenom-log-check .

