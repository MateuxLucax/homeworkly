FROM php:8.0-apache

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update; \
  apt-get -yq upgrade; \
  apt-get install -y --no-install-recommends \
  apt-utils \
  unzip \
  nano; \
  apt-get -yq autoremove; \
  apt-get clean; \
  rm -rf /var/lib/apt/lists/*

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions gd curl mbstring pgsql zip xml pdo_pgsql

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN a2enmod rewrite

EXPOSE 80