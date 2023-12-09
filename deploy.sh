#!/bin/bash
git pull
docker exec paintai bash -c 'cd /paintai && composer install --prefer-dist --no-scripts --no-progress --no-suggest --no-interaction --no-dev && composer dump-autoload --optimize --no-dev'
chown -R web:web .
find . -type d -exec chmod 775 {} \;
find . -type f -exec chmod 664 {} \;
