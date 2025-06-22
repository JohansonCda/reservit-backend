# Etapa 1: Imagen PHP con Composer preinstalado
FROM php:8.3-cli AS base

# Variables de entorno recomendadas
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_HOME=/composer

# Instala Composer manualmente
RUN apt-get update && apt-get install -y curl unzip git zip libicu-dev libzip-dev libxml2-dev default-mysql-client && \
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer && \
    docker-php-ext-install pdo pdo_mysql intl zip

WORKDIR /app

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --no-interaction --optimize-autoloader

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
