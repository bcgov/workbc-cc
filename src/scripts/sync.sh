#! /bin/bash
set -e
composer install
drush cr
drush updb -y
drush cim $1
drush deploy:hook -y
drush cr
