FROM php:8.3.0-apache
ENV DEBIAN_FRONTEND=noninteractive

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN set -eux ;\
    apt-get update ;\
    apt-get install -y zip p7zip-full libzip-dev;\
    docker-php-ext-install \
        mysqli \
        pdo \
        pdo_mysql \
        zip
COPY fs /
