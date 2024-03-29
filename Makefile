serve:
	cd www && PHP_CLI_SERVER_WORKERS=2 php -S localhost:6006 index.php
playground:
	php -S localhost:6007 playground.php
deploy:
	bash deploy.sh
setup:
	# need to do this inside of the docker container...
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"
	composer install
init:
	php init.php
rebuild:
	cd docker && docker compose down && docker compose build swag && docker compose up -d
up:
	cd docker && docker compose up -d
down:
	cd docker && docker compose down
nginxerrors:
	docker exec -it paintai bash -c 'tail -f /config/log/nginx/error.log'
