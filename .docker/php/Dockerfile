FROM php:7.4-fpm

WORKDIR /var/www

RUN apt-get update
RUN apt-get install -y git zlib1g-dev libzip-dev unzip libmemcached-dev libpng-dev libicu-dev g++ curl \
    libonig-dev \
    libjpeg62-turbo-dev libfreetype6-dev

RUN docker-php-ext-install zip
RUN docker-php-ext-install opcache
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl
RUN docker-php-ext-install exif
RUN docker-php-ext-configure exif --enable-exif
# requires libonig-dev
RUN docker-php-ext-install mbstring
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

# requires libpng-dev libjpeg62-turbo-dev libfreetype6-dev
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd

RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini
RUN printf "\n" | pecl install apcu xdebug mongodb memcached
RUN docker-php-ext-enable xdebug apcu mongodb memcached

RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && mv composer.phar /usr/local/bin/composer

EXPOSE 9000
CMD ["php-fpm"]
