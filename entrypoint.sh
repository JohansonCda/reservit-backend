#!/bin/sh

echo "⏳ Esperando a la base de datos en ${DATABASE_HOST:-mysql}..."

until nc -z ${DATABASE_HOST:-mysql} ${DATABASE_PORT:-3306}; do
  sleep 2
done

echo "✅ Base de datos disponible, ejecutando comandos..."

php bin/console doctrine:database:create --if-not-exists
php bin/console doctrine:migrations:migrate --no-interaction
php bin/console doctrine:fixtures:load --no-interaction || true

echo "🚀 Servidor en ejecución en el puerto 8000"
php -S 0.0.0.0:8000 -t public
