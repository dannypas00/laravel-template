SHELL := bash

ENV ?= local

# Replace these values with your own, escape spaces with backslashes (in fullname for example)
GITHUB_URL ?= github\.com\/dannypas00\/laravel-template
PROJECT_NAMESPACE ?= dannypas00
PROJECT_NAME ?= laravel-template-project
DEVELOPER_FULLNAME ?= laravel-template-fullname
DEVELOPER_USERNAME ?= laravel-template-username
DEVELOPER_EMAIL ?= laravel-template@example.com

# Replacement map using sed (see $(TEMPLATES) target below
TEMPLATE_GITHUB_URL = github\.com\/dannypas00\/laravel-template
TEMPLATES = $(TEMPLATE_GITHUB_URL)/$(GITHUB_URL) laravel-template-namespace/$(PROJECT_NAMESPACE) laravel-template-project/$(PROJECT_NAME) laravel-template-fullname/$(DEVELOPER_FULLNAME) laravel-template-username/$(DEVELOPER_USERNAME) laravel-template@example.com/$(DEVELOPER_EMAIL)

NO_DOCKER ?= false

DOCKER ?= docker
DOCKER_COMPOSE ?= docker compose
NODE_LOCAL ?= false

PHP_CONTAINER = $(DOCKER_COMPOSE) exec php
NODE_CONTAINER = $(DOCKER_COMPOSE) exec node

ifeq ($(NO_DOCKER), true)
PHP_CONTAINER =
NODE_LOCAL = true
endif

ifeq ($(ENV), local)
NPM_INSTALL_ARGS =
else
NPM_INSTALL_ARGS = --omit=dev
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
	@(command -v npm > /dev/null) || (echo "NPM not installed" && exit 127)
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
composer.lock: up
ifeq ($(ENV), local)
	$(COMPOSER) install --prefer-dist
else
	$(COMPOSER) install --prefer-dist --no-scripts --no-plugins --no-interaction --no-progress --no-dev --no-suggest --optimize-autoloader
endif
	$(COMPOSER) clear-cache --gc

vendor/autoload.php: up
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
ifeq ($(ENV), local)
	$(DOCKER_COMPOSE) --profile dev pull
	$(DOCKER_COMPOSE) --profile dev build
else
	$(DOCKER_COMPOSE) pull
	$(DOCKER_COMPOSE) build
endif
endif

up:
ifneq ($(NO_DOCKER), true)
ifeq ($(ENV), local)
	$(DOCKER_COMPOSE) --profile dev up -d --remove-orphans
else
	$(DOCKER_COMPOSE) --profile prod up -d --remove-orphans
endif
endif

down:
ifneq ($(NO_DOCKER), true)
ifeq ($(ENV), local)
	$(DOCKER_COMPOSE) --profile dev down
else
	$(DOCKER_COMPOSE) --profile prod down
endif
endif

.PHONY: test-integration
test-integration: up
ifeq ($(ENV), local)
	$(PHP) artisan test --env=integration --testsuite=Integration
endif

.PHONY: app-key
app-key: .env up
	@# Only generate an app key if the .env doesn't have one yet
	(grep "^APP_KEY=$$" .env && $(PHP) artisan key:generate) || true
	grep "^APP_KEY=" .env

resources/js/: package-lock.json
	@# If public/hot is present, laravel will try to serve from vite server
	@rm public/hot || true
	$(NPM) run build

