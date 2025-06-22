#!/bin/sh

set -e

# Esperar a que la base de datos esté lista
until nc -z $DB_HOST $DB_PORT; do
  echo "⏳ Esperando a la base de datos en $DB_HOST:$DB_PORT..."
  sleep 2
done

# Ejecutar comandos Symfony
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction

# Iniciar el servidor web en el puerto 8000
php -S 0.0.0.0:8000 -t public
