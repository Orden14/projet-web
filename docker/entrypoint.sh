#!/bin/bash
set -e

echo "⏳ Attendre la base de données..."
while ! nc -z db 3306; do
  sleep 1
done

echo "✅ Base de données prête"

if [ "$APP_ENV" = "dev" ]; then
    echo "🔄 Exécution des migrations..."
    php bin/console doctrine:database:drop --if-exists --force --no-interaction || true
    php bin/console doctrine:database:create --no-interaction || true
    php bin/console doctrine:migrations:migrate --no-interaction || true
    
    echo "🔄 Génération des données de test..."
    yarn truncate-database || true
fi

echo "✅ Démarrage de PHP-FPM..."
exec php-fpm
