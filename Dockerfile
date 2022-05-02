FROM wdst-ocp-drupal-base:2.3
COPY src /code
RUN chmod -R g+rwX /code
RUN cd /code && rm -rf .git && composer install && composer update
# Install wkhtmltopdf
RUN set -x; \
  apt-get update \
  && apt-get install -y --no-install-recommends \
      curl \
  && curl -o wkhtmltox.deb -sSL https://github.com/wkhtmltopdf/wkhtmltopdf/releases/download/0.12.5/wkhtmltox_0.12.5-1.stretch_amd64.deb \
  && echo '7e35a63f9db14f93ec7feeb0fce76b30c08f2057 wkhtmltox.deb' | sha1sum -c - \
  && dpkg --force-depends -i wkhtmltox.deb\
  && apt-get -y install -f --no-install-recommends \
  && rm -rf /var/lib/apt/lists/* wkhtmltox.deb
