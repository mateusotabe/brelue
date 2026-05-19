ARG PHP_VERSION=8.5

FROM php:${PHP_VERSION}-fpm-alpine AS base
WORKDIR /home/www

# Runtime libs (não remover!)
RUN apk add --no-cache \
  icu-libs \
  libzip \
  libpng \
  freetype \
  libjpeg-turbo \
  libpq

# Build deps (removíveis)
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    icu-dev \
    oniguruma-dev \
    zlib-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    postgresql-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install pdo_pgsql \
  && docker-php-ext-install pgsql \
  && docker-php-ext-install zip \
  && docker-php-ext-install intl \
  && docker-php-ext-install gd \
  && apk del .build-deps

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM base AS dev
RUN apk add --no-cache $PHPIZE_DEPS linux-headers nodejs npm \
  && npm install -g pnpm \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug

CMD ["php-fpm"]