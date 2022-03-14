init:
	docker-compose build --force-rm --no-cache
	make up
up:
	docker-compose up -d
	docker exec app composer install
	docker exec app php bin/console doctrine:database:create --if-not-exists
	docker exec app php bin/console doctrine:migrations:migrate
	docker exec app php -S 0.0.0.0:8000 -t /app/public
sh:
	docker exec -it app sh
