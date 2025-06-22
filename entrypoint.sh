#!/bin/bash
set -e

# Esperar a que MySQL esté disponible (ajusta host si es necesario)
until mysqladmin ping -h"$MYSQL_HOST" --silent; do
  echo "Esperando a MySQL..."
  sleep 2
done

# Crear base de datos y ejecutar migraciones
php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction

# Iniciar servidor Symfony
exec "$@"
