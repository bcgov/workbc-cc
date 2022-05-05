FROM wdst-ocp-drupal-base:2.4
COPY src /code
RUN chmod -R g+rwX /code
RUN cd /code && rm -rf .git && composer install && composer update
