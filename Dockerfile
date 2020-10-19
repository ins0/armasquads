FROM webdevops/php-nginx:7.4

ENV WEB_DOCUMENT_ROOT /app/public
ENV USERNAME wine

# DEP
RUN dpkg --add-architecture i386 \
    && apt-get -y update \
    && apt-get -y install --no-install-recommends \
         software-properties-common \
         ca-certificates \
         default-mysql-client \
         wine32\
         winetricks \
         winbind \
         git-extras \
         rubygems \
         bsdmainutils \
    && apt-get -y update

RUN apt-get -y remove --purge software-properties-common \
    && apt-get -y autoremove --purge \
    && apt-get -y clean \
    && rm -rf /tmp/* /var/tmp/* /var/lib/apt/lists/*

RUN useradd -u 1001 -d /home/${USERNAME} -m -s /bin/bash wine \
    && mkdir /tmp/.X11-unix \
    && chmod 1777 /tmp/.X11-unix

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin/ --filename=composer

RUN gem install git_time_extractor

# APP
WORKDIR /app/

COPY --chown=application:application ./src/composer.json composer.json
RUN composer install --prefer-dist --no-scripts --no-dev --no-autoloader && rm -rf /root/.composer

COPY --chown=application:application ./src/. /app
RUN mkdir ./data/cache/

RUN find /app -type d -exec chmod 755 {} \;
RUN find /app -type f -exec chmod 644 {} \;

RUN groupadd volumes -g 999
RUN usermod -a -G volumes application
RUN usermod -a -G volumes www-data

RUN composer dump-autoload --no-scripts --no-dev --optimize

USER application
ENV WINEARCH win32
RUN winecfg

USER root