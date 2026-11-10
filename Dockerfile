FROM php:8.4-apache

# 1. Instalar dependencias del sistema necesarias para PostgreSQL, extensiones y Node.js
RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip \
    unzip \
    git \
    curl \
    gnupg \
    && rm -rf /var/lib/apt/lists/*

# 2. Instalar Node.js (versión 20.x recomendada para Vite)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 3. Copiar el binario de Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Instalar extensiones de PHP requeridas por Laravel y PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql bcmath

# 5. Habilitar mod_rewrite de Apache para las rutas amigables de Laravel
RUN a2enmod rewrite

# 6. Apuntar el DocumentRoot de Apache a la carpeta public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 7. Establecer el directorio de trabajo
WORKDIR /var/www/html

# 8. Copiar el código fuente asegurando que pertenezca al usuario www-data
COPY --chown=www-data:www-data . /var/www/html

# 9. Instalar dependencias de producción de Composer (sin dev)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 10. Instalar dependencias de Node.js y compilar los assets con Vite
RUN npm install && npm run build

# 11. Ajustar permisos para storage y caché
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 12. Dar permisos de ejecución al script de arranque
RUN chmod +x /var/www/html/docker-entrypoint.sh

# 13. Indicar el script de inicio que arrancará el contenedor
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
