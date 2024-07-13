# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec app
NODE_CONT = $(DOCKER_COMP) run --rm node
DOC_CONT = $(DOCKER_COMP) run --rm docsify

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console
NPM  = $(NODE_CONT) npm

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh install composer vendor console cc npm watch

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the PHP FPM container
	@$(PHP_CONT) sh

install: build up ## Start docker environnement and install php/node js dependencies
	@$(COMPOSER) install
	@$(NPM) install
	echo "Project available at http://localhost:8080."

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

quality: ## Run quality script with ECS and PHPStan
quality: c=run-script quality
quality: composer

test: ## Run quality script with ECS and PHPStan
test: c=run-script test
test: composer


## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
console: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: console

## —— Node 🧊 ——————————————————————————————————————————————————————————————————
npm: ## List all NPM commands or pass the parameter "c=" to run a given command, example: make npm c="run build"
	@$(eval c ?=)
	@$(NPM) $(c)

watch: ## Build assets in development mode with hot-reloading
	@$(NPM) run watch

## —— Docsify 📚 ———————————————————————————————————————————————————————————————
doc: ## Start documentation in local environment (without docker)
	@(docsify serve docs) || echo "\n\e[31minstall Docsify : 'npm i -g docsify-cli'\e[0m\n"

# Thanks https://github.com/dunglas/symfony-docker/blob/main/docs/makefile.md <3
