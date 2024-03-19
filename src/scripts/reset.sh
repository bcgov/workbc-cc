#!/bin/bash
#
# Reset the local Drupal site with a dump from the Backup/Restore files.
#
if [ -z "$1" ]; then
  docker-compose exec php drush bamls --files=private_files
  exit
fi

docker-compose exec -T postgres psql -U drupal workbc-cc < src/scripts/workbc-cc.reset.sql
gunzip -k -c "src/private/backup_migrate/$1" | docker-compose exec -T postgres psql -U drupal workbc-cc
docker-compose exec php drush upwd admin 'password'
docker-compose exec php scripts/sync.sh -y
docker-compose exec php drush en -y devel views_ui
