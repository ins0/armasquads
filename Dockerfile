FROM webdevops/php-nginx:8.4 AS armake

ARG ARMAKE_COMMIT=e4940fa

# The Makefile predates GNU make 4.3 (.RECIPEPREFIX) and GCC 10 (-fno-common by default).
RUN apt-get update \
    && apt-get install -y --no-install-recommends build-essential bison flex git libssl-dev \
    && git clone https://github.com/KoffeinFlummi/armake.git /tmp/armake \
    && git -C /tmp/armake checkout "$ARMAKE_COMMIT" \
    && sed -i -e '1d' -e 's/^    /\t/' -e 's/^CFLAGS = /CFLAGS = -fcommon /' /tmp/armake/Makefile \
    && make -C /tmp/armake \
    && install -m 0755 /tmp/armake/bin/armake /usr/local/bin/armake

FROM composer:2 AS vendor

WORKDIR /app
COPY src/composer.json src/composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --no-autoloader --ignore-platform-reqs
COPY src/app ./app
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

FROM webdevops/php-nginx:8.4

ENV WEB_DOCUMENT_ROOT=/app/public

RUN rm -f /usr/local/etc/php/conf.d/00-ioncube.ini

COPY --from=armake /usr/local/bin/armake /usr/local/bin/armake
COPY docker/nginx/conf.d/ /opt/docker/etc/nginx/conf.d/
COPY docker/nginx/vhost.common.d/ /opt/docker/etc/nginx/vhost.common.d/

WORKDIR /app
COPY --chown=application:application src/app ./app
COPY --chown=application:application src/lang ./lang
COPY --chown=application:application src/public ./public
COPY --chown=application:application src/templates ./templates
COPY --from=vendor --chown=application:application /app/vendor ./vendor
