#!/usr/bin/env bash
set -e

# Directorio de la app
APP_DIR=/var/www/html
cd "$APP_DIR"

# Copiar .env si no existe
if [ ! -f .env ] && [ -f .env.example ]; then
	cp .env.example .env
fi

# Asegurar variables de DB por defecto si no están presentes en .env
if ! grep -q '^DB_CONNECTION=' .env; then echo 'DB_CONNECTION=mysql' >> .env; fi
if ! grep -q '^DB_HOST=' .env; then echo 'DB_HOST=db' >> .env; fi
if ! grep -q '^DB_PORT=' .env; then echo 'DB_PORT=3306' >> .env; fi
if ! grep -q '^DB_DATABASE=' .env; then echo "DB_DATABASE=${DB_DATABASE:-tickets}" >> .env; fi
if ! grep -q '^DB_USERNAME=' .env; then echo "DB_USERNAME=${DB_USERNAME:-tickets}" >> .env; fi
if ! grep -q '^DB_PASSWORD=' .env; then echo "DB_PASSWORD=${DB_PASSWORD:-secret}" >> .env; fi

# Instalar dependencias de Composer si falta vendor
if [ ! -d vendor ]; then
	composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generar APP_KEY si falta (busca valor no vacío)
CURRENT_KEY=$(grep '^APP_KEY=' .env | cut -d '=' -f2- || true)
if [ -z "$CURRENT_KEY" ] || [ "$CURRENT_KEY" = "base64:" ]; then
	php artisan key:generate --force --no-interaction || true
fi

# Esperar DB antes de migrar (reintentos simples)
ATTEMPTS=0
until php -r "new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));" >/dev/null 2>&1 || [ $ATTEMPTS -ge 10 ]; do
	ATTEMPTS=$((ATTEMPTS+1))
	echo "Esperando a la base de datos... intento $ATTEMPTS"
	sleep 3
done

# Migraciones en modo no interactivo
php artisan migrate --force || echo "Migraciones no aplicadas; puedes reintentar manualmente."

# Compilar assets en prod si hay Node
if command -v npm >/dev/null 2>&1 && [ "${APP_ENV}" = "production" ]; then
	[ -d node_modules ] || (npm ci || npm install || true)
	npm run build || true
fi

exec "$@"
