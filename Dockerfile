FROM php:7.2-alpine

RUN apk add --no-cache make $PHPIZE_DEPS \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN echo $'\
xdebug.coverage_enable=1\n\
xdebug.default_enable=1\n\
' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer && \
    curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig && \
    php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" && \
    php /tmp/composer-setup.php && \
    mv composer.phar /usr/local/bin/composer

RUN wget -O phpunit https://phar.phpunit.de/phpunit-7.phar && \
    chmod +x phpunit && \
    mv phpunit /usr/local/bin/phpunit

WORKDIR app
