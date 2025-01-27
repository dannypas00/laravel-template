SHELL := bash

ENV ?= local

# Replace these values with your own, escape spaces with backslashes (in fullname for example)
GITHUB_URL ?= github\.com\/dannypas00\/laravel-template
PROJECT_NAMESPACE ?= dannypas00
PROJECT_NAME ?= laravel-template-project
DEVELOPER_FULLNAME ?= laravel-template-fullname
DEVELOPER_USERNAME ?= laravel-template-username
DEVELOPER_EMAIL ?= laravel-template@example.com

# DO NOT CHANGE THESE BEFORE RUNNING TEMPLATE TARGET
# Replacement map using sed (see $(TEMPLATES) target below)
TEMPLATE_GITHUB_URL = github\.com\/dannypas00\/laravel-template
TEMPLATES = $(TEMPLATE_GITHUB_URL)/$(GITHUB_URL) laravel-template-namespace/$(PROJECT_NAMESPACE) laravel-template-project/$(PROJECT_NAME) laravel-template-fullname/$(DEVELOPER_FULLNAME) laravel-template-username/$(DEVELOPER_USERNAME) laravel-template@example.com/$(DEVELOPER_EMAIL)

NO_DOCKER ?= false

DOCKER ?= docker
DOCKER_COMPOSE ?= docker compose
NODE_LOCAL ?= false

PHP_CONTAINER = $(DOCKER_COMPOSE) run --rm php
NODE_CONTAINER = $(DOCKER_COMPOSE) run --rm node

ifeq ($(NO_DOCKER), true)
PHP_CONTAINER =
NODE_LOCAL = true
endif

ifeq ($(ENV), local)
NPM_INSTALL_ARGS =
COMPOSE_PROFILE = --profile dev
else
NPM_INSTALL_ARGS = --omit=dev
COMPOSE_PROFILE = --profile prod
endif

PHP ?= $(PHP_CONTAINER) php
COMPOSER ?= $(PHP_CONTAINER) composer
NPM ?= $(NODE_CONTAINER) npm

# First target not starting with "." is default target
all: project-setup

.PHONY: template $(TEMPLATES)
template: $(TEMPLATES)
$(TEMPLATES):
	@# Get all files containing the TEMPLATE_PATTERN and replace it with PROJECT_NAME.
	@# This fails if no files are found (script has already run), hence the || true.
	@grep -rl "laravel-template" . --exclude-dir=public/build --exclude-dir=vendor --exclude-dir=node_modules --exclude-dir=.idea --exclude=Makefile --exclude-dir=.git | xargs sed -i 's/$(@)/g' || true

.PHONY: prod clean install deploy project-setup clear-cache dependencies
install: $(TEMPLATES) dependencies .env docker-build composer.lock package-lock.json vendor node_modules up app-key
project-setup: install init-db test-integration vendor/autoload.php

dependencies:
ifeq ($(NODE_LOCAL), true)
	@(command -v npm > /dev/null) || (echo "NPM not installed" && exit 127)
endif
	@(command -v docker > /dev/null) || (echo "Docker not installed" && exit 127)
	@(docker compose > /dev/null) || (echo "Docker compose plugin not installed" && exit 127)

clean:
	$(DOCKER_COMPOSE) down -v
	rm -rf composer.lock package-lock.json vendor node_modules bootstrap/cache/*.php public/build

deploy: install clear-cache resources/js/ migrate vendor/autoload.php up
	$(PHP) artisan optimize

clear-cache: up
	$(PHP) artisan optimize:clear

.env:
	@# Copy env.example file if env file doesn't exist yet
	[[ -f .env ]] || cp .env.example .env

vendor: composer.lock
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

node_modules:
ifeq ($(NODE_LOCAL), true)
	npm ci $(NPM_INSTALL_ARGS)
else
	$(NPM) ci $(NPM_INSTALL_ARGS)
endif

package-lock.json:
ifeq ($(NODE_LOCAL), true)
	npm install $(NPM_INSTALL_ARGS)
else
	$(NPM) install $(NPM_INSTALL_ARGS)
endif

.PHONY: init-db drop-db migrate seed
init-db: up drop-db migrate seed
migrate: database/migrations/
seed: database/seeders/

drop-db: up
ifeq ($(ENV), local)
	$(PHP) artisan db:wipe || true
endif

database/migrations/: composer.lock up
ifeq ($(ENV), local)
	$(PHP) artisan migrate -n
else
	$(PHP) artisan migrate
endif

database/seeders/: drop-db database/migrations/ database/factories/ up
ifeq ($(ENV), local)
	$(PHP) artisan db:seed --class=Database\\Seeders\\DatabaseSeeder
endif

.PHONY: docker-build up down
docker-build:
ifneq ($(NO_DOCKER), true)
	$(DOCKER_COMPOSE) $(COMPOSE_PROFILE) build --pull
endif

up:
ifneq ($(NO_DOCKER), true)
	$(DOCKER_COMPOSE) $(COMPOSE_PROFILE) up -d --remove-orphans --wait
endif

down:
ifneq ($(NO_DOCKER), true)
	$(DOCKER_COMPOSE) $(COMPOSE_PROFILE) down
endif

.PHONY: test-integration
test-integration: up
ifeq ($(ENV), local)
	$(MAKE) up
	sleep 5
	$(PHP) artisan test --env=integration --testsuite=Integration
endif

.PHONY: app-key
app-key: .env up
	@# Only generate an app key if the .env doesn't have one yet
	(grep "^APP_KEY=$$" .env && $(PHP) artisan key:generate && $(DOCKER_COMPOSE) $(COMPOSE_PROFILE) restart frank) || true

resources/js/: package-lock.json
	@# If public/hot is present, laravel will try to serve from vite server
	@rm public/hot || true
	$(NPM) run build

