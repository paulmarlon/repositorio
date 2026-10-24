FROM php:8.4.27-apache

# Instalar dependencias del sistema necesarias para PostgreSQL y Composer
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Copiar el binario de Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar extensiones de PHP para PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite de Apache para las rutas amigables de Laravel
RUN a2enmod rewrite

# Apuntar el DocumentRoot de Apache a la carpeta public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente asegurando que pertenezca al usuario www-data
COPY --chown=www-data:www-data . /var/www/html

# Instalar dependencias de producción de Composer sin scripts interactivos
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Limpiar y reconstruir cachés del framework, y enlazar el storage
RUN php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear \
    && php artisan storage:link

# Ajustar permisos definitivos para storage y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
