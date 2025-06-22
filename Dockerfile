# Etapa base
FROM php:8.2-cli

# Variables de entorno
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=dev

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql intl zip

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Crea el directorio de trabajo
WORKDIR /var/www

# Copia los archivos del proyecto
COPY . .

# Configura Composer
RUN composer config --no-plugins allow-plugins.symfony/scripts-handler false
RUN composer config --no-plugins allow-plugins.symfony/flex true
RUN composer config --unset scripts.post-install-cmd
RUN composer config --unset scripts.post-update-cmd

# Instala dependencias
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Expone el puerto (ajustable)
EXPOSE 8000

