ARG PHP_VERSION
FROM php:${PHP_VERSION}-alpine

RUN apk add --no-cache make

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer && \
    curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig && \
    php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" && \
    php /tmp/composer-setup.php && \
    mv composer.phar /usr/local/bin/composer

ARG WITH_XDEBUG="no"
RUN if [ "$WITH_XDEBUG" = "yes" ];\
    then \
        apk add --no-cache $PHPIZE_DEPS &&\
        pecl install xdebug &&\
        docker-php-ext-enable xdebug &&\
        echo $'\
xdebug.coverage_enable=0\n\
xdebug.remote_autostart=1\n\
xdebug.remote_enable=1\n\
xdebug.remote_host=host.docker.internal\n\
' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini\
    ; fi

WORKDIR app
