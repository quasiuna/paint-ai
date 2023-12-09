serve:
	cd www && php -S localhost:6006
setup:
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
	php composer-setup.php
	php -r "unlink('composer-setup.php');"
	composer install
init:
	php init.php
nginxerrors:
	docker exec -it paintai bash -c 'tail -f /config/log/nginx/error.log'
