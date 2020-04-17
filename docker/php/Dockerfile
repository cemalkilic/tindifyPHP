FROM php:7.4-fpm

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN docker-php-ext-install mysqli pdo pdo_mysql
