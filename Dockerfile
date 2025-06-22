# Etapa base
FROM php:8.2-cli

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=prod

# Instala extensiones necesarias y herramientas
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libxml2-dev \
    default-mysql-client netcat-openbsd \
    && docker-php-ext-install pdo pdo_mysql intl zip

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Crea el directorio de la app
WORKDIR /var/www

# Copia archivos
COPY . .

COPY .env.production .env

# Configura composer para evitar errores en producción
RUN composer config --no-plugins allow-plugins.symfony/scripts-handler false
RUN composer config --no-plugins allow-plugins.symfony/flex true
RUN composer config --unset scripts.post-install-cmd
RUN composer config --unset scripts.post-update-cmd

# Instala dependencias
RUN composer install --no-dev --no-interaction --optimize-autoloader

RUN php bin/console assets:install public --no-interaction --env=prod

RUN composer require doctrine/doctrine-fixtures-bundle
# Ejecuta migraciones y carga de fixtures
RUN php bin/console doctrine:migrations:migrate --no-interaction || true
RUN php bin/console doctrine:fixtures:load --no-interaction || true

# Exponemos el puerto
EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

# Entrypoint para esperar la DB y ejecutar fixtures
#COPY entrypoint.sh /entrypoint.sh
#RUN chmod +x /entrypoint.sh

#CMD ["/entrypoint.sh"]
