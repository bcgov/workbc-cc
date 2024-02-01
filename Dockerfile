FROM 075458558257.dkr.ecr.ca-central-1.amazonaws.com/drupal-base:1.0
ARG TIMEOUT
ARG MAX_CHILDREN
RUN sed -i '/;slowlog/c\slowlog = /var/log/slow.log' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i '/;request_slowlog_timeout/c\request_slowlog_timeout = $TIMEOUT' /usr/local/etc/php-fpm.d/www.conf
RUN sed -i '/pm.max_children = 5/c\pm.max_children = $MAX_CHILDREN' /usr/local/etc/php-fpm.d/www.conf
COPY src /code
RUN chmod -R g+rwX /code
RUN cd /code && rm -rf .git && composer install && composer update
