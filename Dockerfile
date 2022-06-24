FROM php:8.1-fpm-alpine

RUN apk --update add wget \
  libmemcached-dev \
  libmcrypt-dev \
  libxml2-dev \
  imagemagick-dev \
  pcre-dev \
  libtool \
  curl-dev \
  libzip-dev \
  make \
  autoconf \
  g++ \
  cyrus-sasl-dev \
  libgsasl-dev \
  oniguruma-dev

RUN docker-php-ext-install curl \
    mysqli \
    mbstring \
    pdo \
    pdo_mysql \
    xml \
    zip \
    bcmath

RUN pecl channel-update pecl.php.net \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-configure zip


RUN echo "Extensions install - start" \
    # Sockets
    && docker-php-ext-install sockets \
    # Amqp
    && apk add --no-cache --update rabbitmq-c-dev \
    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && pecl install -o -f amqp \
    && docker-php-ext-enable amqp \
    && apk del .phpize-deps \
    #
    && echo "Extensions install - finish"

RUN curl https://getcomposer.org/installer --output composer-setup.php \
    && php composer-setup.php \
    && mv composer.phar /usr/local/bin/composer \
    && php -r "unlink('composer-setup.php');"

WORKDIR /var/www
RUN addgroup --system --gid 1000 www
RUN adduser -S -u 1000 -g "" www -G www-data -G www
COPY --chown=www:www . /var/www
# RUN chmod -R 777 /var/www/storage/logs
USER www

CMD ["php-fpm"]
