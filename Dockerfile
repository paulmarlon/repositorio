# Partimos de la imagen oficial de PHP 8.4 con Apache preinstalado
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

# 2. Instalar Node.js (versión 20.x recomendada para Vite y compilación de frontend)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# 3. Copiar el binario oficial de Composer desde su imagen oficial para gestionar dependencias PHP
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Instalar extensiones de PHP requeridas por Laravel y bases de datos PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql bcmath

# 5. Habilitar mod_rewrite de Apache para que funcionen las URLs amigables (routing) de Laravel
RUN a2enmod rewrite

# 6. Desactivar el módulo mpm_event y activar mpm_prefork para evitar el conflicto "AH00534: More than one MPM loaded" con PHP
RUN a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork

# 7. Apuntar el DocumentRoot por defecto de Apache a la carpeta /public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 8. Establecer el directorio de trabajo predeterminado dentro del contenedor
WORKDIR /var/www/html

# 9. Copiar todo el código fuente del proyecto asegurando que pertenezca al usuario www-data
COPY --chown=www-data:www-data . /var/www/html

# 10. Instalar las dependencias de producción de Composer optimizando el autoloader (sin paquetes de desarrollo)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 11. Instalar las dependencias de Node.js (npm) y compilar los assets de la aplicación con Vite
RUN npm install && npm run build

# 12. Ajustar permisos de escritura y lectura para las carpetas críticas de almacenamiento y caché de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 13. Otorgar permisos de ejecución al script personalizado de inicio (entrypoint)
RUN chmod +x /var/www/html/docker-entrypoint.sh

# 14. Definir el script de arranque principal que ejecutará migraciones/cachés y levantará Apache
ENTRYPOINT ["/var/www/html/docker-entrypoint.sh"]
