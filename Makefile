release: build up install-dependencies

up:
	docker-compose up -d

build:
	docker-compose build

migrate:
	docker-compose exec app php bin/console doctrine:migrations:migrate

install-dependencies:
	docker-compose exec app composer install $(DEPENDENCIES_INSTALL_OPTIONS)
	docker-compose exec app composer dump-autoload