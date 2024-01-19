FROM php:8.3-fpm

WORKDIR /var/www/app

RUN apt update -y && \
    apt upgrade -y && \
    apt install -y libpq-dev \
        libzip-dev \
        zip

RUN docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql pgsql

RUN docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer