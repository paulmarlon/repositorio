#!/bin/bash
set -e

# Configurar Apache para que escuche en el puerto dinámico asignado por Render
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf

# Ejecutar optimizaciones de Laravel con las variables de producción cargadas
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones en Supabase automáticamente
php artisan migrate --force

# Iniciar Apache en primer plano
exec apache2-foreground
