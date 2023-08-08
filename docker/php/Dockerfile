FROM php:8.1-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Установка PHP zip extension
RUN apt-get install -y \
        libzip-dev \
        zip \
  && docker-php-ext-install zip

# Установка xDebug и его конфигурация
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
