FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git unzip zip curl libicu-dev libzip-dev libxml2-dev default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql intl zip

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    composer --version

WORKDIR /app

COPY . .

RUN composer install --no-dev --no-interaction --optimize-autoloader

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
