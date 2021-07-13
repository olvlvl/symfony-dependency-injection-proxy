ARG PHP_VERSION
FROM php:${PHP_VERSION}-alpine

RUN apk add --no-cache make

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer && \
    curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig && \
    php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" && \
    php /tmp/composer-setup.php && \
    mv composer.phar /usr/local/bin/composer

RUN apk add --no-cache $PHPIZE_DEPS &&\
    pecl install xdebug &&\
    docker-php-ext-enable xdebug &&\
    echo $'\
xdebug.client_host=host.docker.internal\n\
xdebug.mode=develop\n\
xdebug.start_with_request=yes\n\
' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR app
