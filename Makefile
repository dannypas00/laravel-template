.PHONY: install up migrate

DOCKER := docker compose
PHP_CONTAINER := php
COMPOSER := $(DOCKER) run --remove-orphans --rm $(PHP_CONTAINER) composer
PHP := $(DOCKER) run --remove-orphans --rm $(PHP_CONTAINER) php

install: .env vendor

.env:
	[[ -f .env ]] || cp .env.example .env

vendor: composer.lock
	$(COMPOSER) install

composer.lock: composer.json

up: docker-compose.yaml vendor .env
	$(DOCKER) up -d

migrate: database/migrations
	$(PHP) artisan migrate

seed:
	make -B migrate
	$(PHP) artisan db:seed
