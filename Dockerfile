ARG PHP_VERSION
FROM php:${PHP_VERSION}-alpine

RUN apk add --no-cache make $PHPIZE_DEPS &&\
    pecl install xdebug &&\
    docker-php-ext-enable xdebug &&\
    echo $'\
xdebug.client_host=host.docker.internal\n\
xdebug.mode=develop\n\
xdebug.start_with_request=yes\n\
' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -s https://raw.githubusercontent.com/composer/getcomposer.org/76a7060ccb93902cd7576b67264ad91c8a2700e2/web/installer | php -- --quiet && \
    mv composer.phar /usr/local/bin/composer

ENV PATH /root/.composer/vendor/bin:$PATH

RUN composer global require squizlabs/php_codesniffer

WORKDIR app
