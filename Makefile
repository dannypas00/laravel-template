SHELL := bash

ENV ?= local
TEMPLATE_GITHUB_URL = github\.com\/dannypas00\/laravel-template

# Replace these values with your own, escape spaces with backslashes (in fullname for example)
GITHUB_URL ?= github\.com\/dannypas00\/laravel-template
PROJECT_NAMESPACE ?= dannypas00
PROJECT_NAME ?= laravel-template-project
DEVELOPER_FULLNAME ?= laravel-template-fullname
DEVELOPER_USERNAME ?= laravel-template-username
DEVELOPER_EMAIL ?= laravel-template@example.com

# Replacement map using sed (see $(TEMPLATES) target below
TEMPLATES = $(TEMPLATE_GITHUB_URL)/$(GITHUB_URL) laravel-template-namespace/$(PROJECT_NAMESPACE) laravel-template-project/$(PROJECT_NAME) laravel-template-fullname/$(DEVELOPER_FULLNAME) laravel-template-username/$(DEVELOPER_USERNAME) laravel-template@example.com/$(DEVELOPER_EMAIL)

NO_DOCKER ?= false

DOCKER ?= docker
DOCKER_COMPOSE ?= $(DOCKER) compose

PHP_CONTAINER = $(DOCKER_COMPOSE) run php

ifeq ($(NO_DOCKER), true)
PHP_CONTAINER =
endif

PHP ?= $(PHP_CONTAINER) php
COMPOSER ?= $(PHP_CONTAINER) composer
NPM ?= $(PHP_CONTAINER) npm

.DEFAULT_TARGET: project-setup

.PHONY: project-setup
project-setup: $(TEMPLATES) dependencies .env.example composer.json package.json app-key init-db resources/js/ test-integration vendor/autoload.php docker-compose.yaml

.PHONY: install
install: composer.lock package-lock.json docker-compose.yaml

.PHONY: deploy
deploy: clear-cache dependencies $(TEMPLATES) .env.example install resources/js/ migrate vendor/autoload.php
	$(PHP) artisan optimize

.PHONY: clear-cache
clear-cache:
	$(PHP) artisan optimize:clear

.PHONY: dependencies
dependencies:
	@(command -v npm > /dev/null) || (echo "NPM not installed" && exit 127)
	@(command -v docker > /dev/null) || (echo "Docker not installed" && exit 127)
	@(docker compose > /dev/null) || (echo "Docker compose plugin not installed" && exit 127)

.PHONY: template $(TEMPLATES)
template: $(TEMPLATES)
$(TEMPLATES):
	@# Get all files containing the TEMPLATE_PATTERN and replace it with PROJECT_NAME.
	@# This fails if no files are found (script has already run), hence the || true.
	@grep -rl "laravel-template-" . --exclude-dir=public/build --exclude-dir=vendor --exclude-dir=node_modules --exclude-dir=.idea --exclude=Makefile --exclude-dir=.git | xargs sed -i 's/$(@)/g' || true

.env.example:
	@# Copy env.example file if env file doesn't exist yet
	[[ -f .env ]] || cp .env.example .env

composer.json: composer.lock
composer.lock:
ifeq ($(ENV), local)
	$(COMPOSER) install --prefer-dist
else
	$(COMPOSER) install --prefer-dist --no-scripts --no-plugins --no-interaction --no-progress --no-dev --no-suggest --optimize-autoloader
endif
	$(COMPOSER) clear-cache --gc

vendor/autoload.php:
ifeq ($(ENV), local)
	$(COMPOSER) dump-autoload
else
	$(COMPOSER) dump-autoload --classmap-authoritative --apcu
endif

package.json:
ifeq ($(ENV), local)
	$(NPM) install
else
	$(NPM) install --omit=dev
endif

package-lock.json:
ifeq ($(ENV), local)
	$(NPM) install
else
	$(NPM) ci --omit=dev
endif

.PHONY: init-db drop-db migrate seed
init-db: drop-db docker-compose.yaml migrate seed
migrate: database/migrations/
seed: database/seeders/

drop-db:
ifeq ($(ENV), local)
ifeq ($(NO_DOCKER), true)
	$(PHP) artisan db:wipe || true
else
	$(DOCKER_COMPOSE) down -v
endif
endif

database/migrations/: composer.lock docker-compose.yaml
ifeq ($(ENV), production)
	$(PHP) artisan migrate
else
	$(PHP) artisan migrate -n
endif

database/seeders/: drop-db database/migrations/ database/factories/
ifeq ($(ENV), local)
	$(PHP) artisan db:seed --class=Database\\Seeders\\DatabaseSeeder
else
	$(PHP) artisan db:seed --class=Database\\Seeders\\LiveSeeder
endif

docker-compose.yaml:
ifneq ($(NO_DOCKER), true)
	$(DOCKER_COMPOSE) up -d --wait --remove-orphans
endif

.PHONY: test-integration
test-integration: docker-compose.yaml
	$(PHP) artisan test --env=integration --testsuite=Integration

.PHONY: app-key
app-key:
	@# Only generate an app key if the .env doesn't have on yet
	(grep "^APP_KEY=$$" .env && $(PHP) artisan key:generate) || true

resources/js/: package-lock.json
	$(NPM) run build
