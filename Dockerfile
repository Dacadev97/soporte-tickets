# Etapa base con PHP 8.2 y Apache
FROM php:8.2-apache

# Instalar dependencias del sistema y extensiones requeridas
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libpng-dev libonig-dev \
        libicu-dev zlib1g-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip bcmath \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Configurar DocumentRoot a public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e "s!<Directory /var/www/>!<Directory ${APACHE_DOCUMENT_ROOT}>!g" /etc/apache2/apache2.conf \
    && sed -ri -e "s!AllowOverride None!AllowOverride All!g" /etc/apache2/apache2.conf

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . /var/www/html

# Ajustar permisos para Laravel
RUN chown -R www-data:www-data /var/www/html \
    && find storage -type d -exec chmod 775 {} \; \
    && find storage -type f -exec chmod 664 {} \; \
    && chmod -R 775 storage bootstrap/cache

# Variables de entorno por defecto
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS=1 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=10000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=128 \
    PHP_OPCACHE_INTERNED_STRINGS_BUFFER=8

# Exponer puerto Apache
EXPOSE 80

# Comando por defecto: preparar app y lanzar Apache
CMD ["bash", "-lc", "set -e; cd /var/www/html; if [ ! -f .env ] && [ -f .env.example ]; then cp .env.example .env; fi; if ! grep -q '^DB_CONNECTION=' .env; then echo 'DB_CONNECTION=mysql' >> .env; fi; if ! grep -q '^DB_HOST=' .env; then echo \"DB_HOST=${DB_HOST:-db}\" >> .env; fi; if ! grep -q '^DB_PORT=' .env; then echo \"DB_PORT=${DB_PORT:-3306}\" >> .env; fi; if ! grep -q '^DB_DATABASE=' .env; then echo \"DB_DATABASE=${DB_DATABASE:-tickets}\" >> .env; fi; if ! grep -q '^DB_USERNAME=' .env; then echo \"DB_USERNAME=${DB_USERNAME:-tickets}\" >> .env; fi; if ! grep -q '^DB_PASSWORD=' .env; then echo \"DB_PASSWORD=${DB_PASSWORD:-secret}\" >> .env; fi; if [ ! -d vendor ]; then composer install --no-interaction --prefer-dist --optimize-autoloader; fi; CURRENT_KEY=$(grep '^APP_KEY=' .env | cut -d '=' -f2- || true); if [ -z \"$CURRENT_KEY\" ] || [ \"$CURRENT_KEY\" = \"base64:\" ]; then php artisan key:generate --force --no-interaction || true; fi; chown -R www-data:www-data storage bootstrap/cache || true; find storage -type d -exec chmod 775 {} \\; || true; find storage -type f -exec chmod 664 {} \\; || true; chmod -R 775 bootstrap/cache || true; ATTEMPTS=0; until php -r \"new PDO('mysql:host=' . (getenv('DB_HOST') ?: 'db') . ';port=' . (getenv('DB_PORT') ?: '3306'), getenv('DB_USERNAME') ?: 'tickets', getenv('DB_PASSWORD') ?: 'secret');\" >/dev/null 2>&1 || [ $ATTEMPTS -ge 10 ]; do ATTEMPTS=$((ATTEMPTS+1)); echo \"Esperando a la base de datos... intento $ATTEMPTS\"; sleep 3; done; php artisan migrate --force || echo \"Migraciones no aplicadas; puedes reintentar manualmente.\"; if command -v npm >/dev/null 2>&1 && [ \"${APP_ENV}\" = \"production\" ]; then [ -d node_modules ] || (npm ci || npm install || true); npm run build || true; fi; exec apache2-foreground"]
