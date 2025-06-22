# Etapa 1: Composer
FROM composer:2 AS composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Etapa 2: PHP con extensiones
FROM php:8.3-cli

WORKDIR /app

# Paso 1: actualizaciones del sistema
RUN apt-get update

# Paso 2: instalar paquetes del sistema
RUN apt-get install -y \
    git unzip zip libicu-dev libzip-dev libxml2-dev default-mysql-client

# Paso 3: instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_mysql intl zip

# Copiar código del proyecto
COPY . .

# Copiar dependencias
COPY --from=composer /app/vendor /app/vendor

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
