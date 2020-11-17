## Composer команды
install:
	composer install
lint:
	composer run-script phpcs -- --standard=data/cs-ruleset.xml public src tests

## Docker команды
start:
	docker-compose up web
test:
	docker-compose up tests
terminal:
	docker-compose run terminal