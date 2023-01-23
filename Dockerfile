FROM php:apache

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY composer.* /var/www/html

RUN install-php-extensions gd
RUN apt-get update && apt-get install -y --no-install-recommends unzip \
    libssl-dev \
    openssl \
    ssl-cert

RUN pecl install mongodb \
    &&  echo "extension=mongodb.so" > $PHP_INI_DIR/conf.d/mongo.ini
RUN docker-php-ext-install opcache
RUN composer install --no-dev

RUN a2enmod rewrite

COPY index.php /var/www/html/
COPY .htaccess /var/www/html/
COPY bin/* /var/www/html/bin/
COPY iiif/* /var/www/html/iiif/
COPY page/* /var/www/html/page/

RUN mkdir /var/www/html/cache