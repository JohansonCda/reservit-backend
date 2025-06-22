FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    git unzip zip curl libicu-dev libzip-dev libxml2-dev default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql intl zip

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Variables necesarias para entornos de producción
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV SYMFONY_SKIP_VERSIONS_CHECK=1
ENV SYMFONY_ALLOW_APP_SCRIPTS=1
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Desactivar plugins de Symfony que causan errores
RUN composer config --no-plugins allow-plugins.symfony/scripts-handler false
RUN composer config --no-plugins allow-plugins.symfony/flex false
RUN composer config scripts.post-install-cmd []
RUN composer config scripts.post-update-cmd []

WORKDIR /app
COPY . .

RUN composer install --no-dev --no-interaction --optimize-autoloader

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
