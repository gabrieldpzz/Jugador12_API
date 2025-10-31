# Jugador12_API

# Ver logs de Nginx (Documentalo)

docker compose logs --tail=100 web

# Ver logs de PHP-FPM (Documentalo)

docker compose logs --tail=100 app

# Confirma conexión a Postgres:

docker compose exec app php artisan tinker --execute "print_r(DB::select('select version()'))"

# Crear Laravel en ./app con la imagen composer:2 (Documentalo)

docker run --rm -v "${PWD}\app:/app" -w /app composer:2 `
composer create-project laravel/laravel .

# Crear Laravel en ./app con la imagen composer:2 (Documentalo)

docker run --rm -v "${PWD}\app:/app" -w /app composer:2 `
composer create-project laravel/laravel .

# Build + up en segundo plano (Documentalo)

docker compose up -d --build

# Instalar dependencias PHP (Documentalo)

docker compose exec app composer install --no-interaction --prefer-dist --optimize-autoloader

# Generar APP_KEY (Documentalo)

docker compose exec app php artisan key:generate

# Migraciones para validar conexión a Postgres (Documentalo)

docker compose exec app php artisan migrate --force

# Probar home

curl -I http://localhost

# EN CASO DE QUE EL CURL DE ERROR:

# Comprueba si hay clave de aplicación

docker compose exec app sh -lc "grep -E '^APP_KEY=' .env || true" # (Documentalo)

# Si ves APP_KEY= vacío o no aparece, genera una:

docker compose exec app php artisan key:generate # (Documentalo)

# Arregla permisos de Laravel (seguro hacerlo siempre)

docker compose exec app sh -lc "chown -R www-data:www-data storage bootstrap/cache"

docker compose exec app sh -lc "chmod -R ug+rwX storage bootstrap/cache"

# Limpia cachés por si hay configuraciones antiguas

docker compose exec app php artisan optimize:clear # (Documentalo)

# Crea el healthcheck dentro del contenedor (para asegurar ruta)

docker compose exec web sh -lc "printf OK > /var/www/html/public/health.txt" # (Documentalo)

# Ahora prueba:

curl http://localhost/health.txt # (Documentalo)
