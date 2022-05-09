FROM 266795317183.dkr.ecr.ca-central-1.amazonaws.com/drupal-base:1.0
COPY src /app
RUN chmod -R +rwX /app
RUN cd /app && rm -rf .git && composer install && composer update
VOLUME [/app]
