UNAME = $(shell uname)

app.check_style:
	make app.code_style
	make app.phpmd

app.code_style:
	docker exec -t php_transactions vendor/bin/phpcs --standard=PSR2 --error-severity=1 --warning-severity=8 --ignore=src/Migrations/ src/ -p --colors

app.phpcbf:
	docker exec -it php_transactions vendor/bin/phpcbf --standard=PSR2 src/

app.phpmd:
	docker exec -t php_transactions vendor/bin/phpmd src/ text phpmd.xml --exclude src/Migrations/,tests/

app.tests.all:
	make app.phpunit
	make app.behat

app.phpunit:
	docker exec -t php_transactions /srv/transactions/bin/phpunit

app.behat:
	docker exec -t php_transactions vendor/bin/behat --no-interaction

app.build.ci:
	make app.docker.set_file_permissions
	make app.docker_compose.build
	make app.composer.install
	make app.doctrine_migrations.ci
	make app.doctrine.load_fixtures
	cp behat.yml.dist behat.yml

app.build.dev.no_docker:
	make app.composer.install
	make app.doctrine_migrations
	make app.doctrine.load_fixtures

app.build.dev:
	$(shell if [ "$(shell uname)" = 'Linux' ]; \
            then make app.docker.set_file_permissions \
            else echo 'not on linux'; \
            fi)

	make app.docker_compose.build
	make app.build.dev.no_docker

app.composer.install:
	docker exec -t php_transactions composer install --optimize-autoloader

app.docker_compose.build:
	docker-compose -f docker-compose.yml pull
	docker-compose -f docker-compose.yml up -d

app.doctrine_migrations.ci:
	docker exec -t php_transactions bin/console d:m:m --allow-no-migration -n -vvv

app.doctrine_migrations:
	docker exec -it php_transactions bin/console d:m:m

app.doctrine.load_fixtures:
	docker exec -t php_transactions bin/console doctrine:fixtures:load -n --purge-with-truncate

app.docker.set_file_permissions:
	setfacl -R -m u:`whoami`:rwx -m g:`whoami`:rwx -m o:rwx -m m:rwx . && setfacl -R -d -m u:`whoami`:rwx -m g:`whoami`:rwx -m o:rwx -m m:rwx . 2>/dev/null

app.docker_compose.ci_cleanup:
	make app.docker_compose.stop
	make app.docker_compose.remove

app.docker_compose.stop:
	docker-compose -f docker-compose.yml stop

app.docker_compose.remove:
	docker-compose -f docker-compose.yml rm -vf

app.docker.sh:
	docker exec -it php_transactions /bin/sh
