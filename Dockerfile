# Etapa base
FROM php:8.2-cli

# Variables de entorno recomendadas
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV APP_ENV=prod

# Instala extensiones necesarias y herramientas
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libzip-dev libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql intl zip

# Instala Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Crea el directorio de la aplicación
WORKDIR /var/www

# Copia los archivos del proyecto
COPY . .

# Configura composer (esto debe ir después del COPY)
RUN composer config --no-plugins allow-plugins.symfony/scripts-handler false
RUN composer config --no-plugins allow-plugins.symfony/flex true
RUN composer config --unset scripts.post-install-cmd
RUN composer config --unset scripts.post-update-cmd

# Instala las dependencias
RUN composer install --no-dev --no-interaction --optimize-autoloader

# Puerto que expondrá el servidor Symfony
EXPOSE 8000

# Comando por defecto (puedes usar también Apache o Nginx según setup)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
¿
COPY entrypoint.sh /app/entrypoint.sh
ENTRYPOINT ["/app/entrypoint.sh"]
CMD ["symfony", "server:start", "--no-interaction", "--allow-http", "--port=8000"]
