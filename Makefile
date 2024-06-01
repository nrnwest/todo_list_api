PHP_CLI = todo_list_api_php-cli
DOCKER_COMPOSE = docker-compose -f docker-compose.yml --env-file ./_docker/.env
DOCKER_COMPOSE_PHP_CLI_EXEC = ${DOCKER_COMPOSE} run -it -u www ${PHP_CLI}

WELCOME = Welcome	"\n\n"http://localhost:4441"\n\n"

build:
	${DOCKER_COMPOSE} build

start:
	${DOCKER_COMPOSE} start

stop:
	${DOCKER_COMPOSE} stop

up:
	${DOCKER_COMPOSE} up -d

down:
	${DOCKER_COMPOSE} down -v --rmi=all --remove-orphans

php_cli:
	${DOCKER_COMPOSE} run -it -u www ${PHP_CLI} bash

composer_install:
	${DOCKER_COMPOSE} run -it -u www ${PHP_CLI} composer install --optimize-autoloader

db_seed:
	${DOCKER_COMPOSE} run -it -u www ${PHP_CLI} php artisan db:seed

db_migrate_fresh:
	${DOCKER_COMPOSE} run -it -u www ${PHP_CLI} php artisan migrate:fresh --seed

init:
	make build up composer_install db_migrate_fresh wprint

# install new project
install:
	make build up wprint

test:
	${DOCKER_COMPOSE} run --rm -it -u www ${PHP_CLI} php artisan test

swagger:
	${DOCKER_COMPOSE} run -it -u www ${PHP_CLI} php artisan l5-swagger:generate

wprint:
	@echo ${WELCOME}
