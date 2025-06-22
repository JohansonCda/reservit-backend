# Etapa 1: Composer con dependencias
FROM composer:2 AS composer

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Etapa 2: PHP con extensiones necesarias
FROM php:8.3-cli

# Instalar extensiones necesarias y herramientas del sistema
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libxml2-dev default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql intl zip

WORKDIR /app

# Copiar el código fuente del proyecto
COPY . .

# Copiar las dependencias desde la etapa anterior
COPY --from=composer /app/vendor /app/vendor

# Puerto donde corre Symfony
EXPOSE 8000

# Comando por defecto para ejecutar Symfony
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
