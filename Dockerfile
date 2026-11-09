FROM php:8.4-apache

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

# Instalar extensiones de PHP requeridas por Laravel y PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Habilitar mod_rewrite de Apache para las rutas amigables de Laravel
RUN a2enmod rewrite

# Apuntar el DocumentRoot de Apache a la carpeta public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Copiar el código fuente asegurando que pertenezca al usuario www-data
COPY --chown=www-data:www-data . /var/www/html

# Instalar dependencias de producción de Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ajustar permisos para storage y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Dar permisos de ejecución al script de arranque
RUN chmod +x /var/www/html/docker-entrypoint.sh

# Indicar el script de inicio que arrancará el contenedor
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
