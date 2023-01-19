FROM php:7.4-fpm

RUN apt-get update -y && apt-get install -y openssl zip unzip git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html/

COPY . /var/www/html/

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install

EXPOSE 9000

CMD ["php-fpm"]