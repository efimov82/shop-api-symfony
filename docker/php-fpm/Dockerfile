#FROM php:7.4-fpm
FROM serversideup/php:8.2-fpm-nginx

RUN apt-get update && apt-get install -y \
    libpq-dev \
    wget \
    zlib1g-dev \
    libmcrypt-dev \
    libzip-dev \
    openssl

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /symfony