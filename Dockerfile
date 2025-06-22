# Imagen base con PHP 8.3
FROM php:8.3-cli

# Instala extensiones de PHP requeridas por Symfony y MySQL
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libxml2-dev default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql intl zip

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Define el directorio de trabajo
WORKDIR /app

# Copia el proyecto al contenedor
COPY . .

# Instala las dependencias de PHP (modo producción)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Puerto expuesto para Railway (Symfony en 8000)
EXPOSE 8000

# Comando para correr Symfony en modo producción
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
