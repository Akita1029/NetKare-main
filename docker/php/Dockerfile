FROM php:8.0.2-fpm

WORKDIR /var/www/project

COPY . /var/www/project

RUN mv /var/www/project/docker/php/custom.ini "$PHP_INI_DIR/conf.d/custom.ini"

COPY --from=composer \
    /usr/bin/composer \
    /usr/bin/composer

RUN usermod -u 1000 www-data;

RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    unzip

RUN docker-php-ext-install \
    intl \
    opcache \
    pdo_mysql \
    zip

# APCu
RUN pecl install apcu \
    && docker-php-ext-enable apcu

# gd
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# amqp
RUN apt-get update && apt-get install -y \
    librabbitmq-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp

# imagick
RUN apt-get update && apt-get install -y \
    libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# 
# RUN apt-get update && apt-get install -y \
#     git
