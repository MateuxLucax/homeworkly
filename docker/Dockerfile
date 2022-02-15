FROM php:8.1.2-apache

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

RUN install-php-extensions pgsql pdo_pgsql

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN a2enmod rewrite
RUN a2enmod ssl

COPY ./docker/cert/ssl.crt /etc/apache2/ssl/ssl.crt
COPY ./docker/cert/ssl.key /etc/apache2/ssl/ssl.key
RUN mkdir -p /var/run/apache2/

EXPOSE 80
EXPOSE 443