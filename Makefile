include .env

help:
	@echo ""
	@echo "usage: make COMMAND:"
	@echo ""
	@echo "Commands:"
	@echo "  run-app        Fast Start"

install:
	@make build
	@make up
	@make cache-clear
	@make user-info

user-info:
	@echo "##########"
	@echo "Admin login: ${CMS_ADMIN_USERNAME}"
	@echo "Admin password: ${CMS_ADMIN_PASSWORD}"
	@echo "Manager url: http://127.0.0.1:9001/manager/"
	@echo "Phpmyadmin url: http://127.0.0.1:9001/phpmyadmin/"
	@echo "##########"

up:
	docker compose up -d
build:
	docker compose build
remake:
	@make destroy
	@make install
stop:
	docker compose stop
down:
	docker compose down --remove-orphans
restart:
	@make down
	@make up
destroy:
	docker compose down --volumes --remove-orphans
destroy-all:
	docker compose down --rmi all --volumes --remove-orphans
ps:
	docker compose ps
logs:
	docker compose logs
mysql:
	docker compose exec mysql bash
app:
	docker compose exec app bash
rollback:
	docker compose exec app composer rollback
composer:
	docker compose exec app composer install
cache-clear:
	docker compose exec app bash -c 'rm -rf core/cache'


#######################
# Extras package
#######################
package-build:
	docker compose exec app bash -c "export PACKAGE_DEPLOY=False && php Extras/${PACKAGE_NAME}/_build/build.php"

package-install:
	docker compose exec app bash -c "php ./docker/app/scripts/checking-add-ons.php"
	@make cache-clear

package-build-deploy:
	docker compose exec app bash -c "export PACKAGE_DEPLOY=True && php Extras/${PACKAGE_NAME}/_build/build.php"

package-target-clear:
	docker compose exec app bash -c 'rm -rf target/*'

package-deploy:
	@make package-target-clear
	@make package-build
	@make package-build-deploy


package-create-new:
	docker run --rm -ePACKAGE_NAME="${PACKAGE_NAME}" -v ./Extras:/var/www/html/Extras webnitros/modx-app:latest php Extras/myapp/rename_it.php


#######################
# Gitify
#######################
gitify-download:
	docker compose exec app bash -c 'gitify modx:download'

gitify-package-install:
	docker compose exec app bash -c 'gitify package:install --all'
	@make cache-clear

gitify-backup:
	docker compose exec app bash -c "gitify backup --compress"
	@echo "Dump created /_backup/"

gitify-restore:
	docker compose exec app bash -c "gitify restore last"


test:
	@make destroy
	docker compose -f docker-compose.test.yml up -d

# Fast start
run-app:
	@make package-create-new
	@make remake
	sleep 3
	@make package-build
	@make package-install
	@make user-info
