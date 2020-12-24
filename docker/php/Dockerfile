FROM php:7.3-fpm-alpine3.10

RUN apk --update add --no-cache freetype-dev \
    libpng libpng-dev libjpeg-turbo-dev \
    imagemagick-dev \
    libzip-dev zip \
    curl-dev

RUN apk add --no-cache --virtual \
    .phpize-deps $PHPIZE_DEPS imagemagick-dev libtool \
    && export CFLAGS="$PHP_CFLAGS" CPPFLAGS="$PHP_CPPFLAGS" LDFLAGS="$PHP_LDFLAGS" \
    && pecl install imagick && docker-php-ext-enable imagick \
    && pecl install redis-5.1.1 && docker-php-ext-enable redis \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-configure gd \
            --with-gd \
            --with-freetype-dir=/usr/include/ \
            --with-png-dir=/usr/include/ \
            --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install curl zip mbstring pdo_mysql gd sockets \
    && apk add --no-cache --virtual .imagick-runtime-deps imagemagick \
    && apk del .phpize-deps

RUN curl -s -o /usr/local/bin/composer https://getcomposer.org/composer.phar \
    && chmod 0755 /usr/local/bin/composer

RUN curl -LsS https://codeception.com/codecept.phar -o /usr/local/bin/codecept \
    && chmod 0755 /usr/local/bin/codecept

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

WORKDIR /code
