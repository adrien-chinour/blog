EXEC=docker compose exec web bash -c
ECHO_START=\n\e[1;37m$(tput bold)
ECHO_END=\e[0m\n$(tput sgr0)

.DEFAULT_GOAL:=help

##@ Install
start: ## Build and start docker environment
	docker compose build
	docker compose up -d
	@echo "$(ECHO_START)✅ Docker environment is ready.$(ECHO_END)"

stop: ## Stop docker environment
	docker compose stop
	@echo "$(ECHO_START)✅ Docker environment is stopped.$(ECHO_END)"

init: ## Run init scripts
	$(EXEC) 'composer install'
	@echo "$(ECHO_START)✅ Local php environment is ready.$(ECHO_END)"

install: start init ## Start and init
	@echo "$(ECHO_START)🚀 Ready to code !$(ECHO_END)"
##<

##@ Utils
sh: ## Start bash on container
	docker compose exec -it web bash

quality: ## Run quality scripts
	$(EXEC) 'composer run-script quality'
	@echo "$(ECHO_START)✅ All check succeed.$(ECHO_END)"
##<

## Help
.PHONY: help
help:
	@awk 'BEGIN {FS = ":.*##"; printf "Usage: make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-25s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)
##<
