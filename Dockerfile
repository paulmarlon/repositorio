FROM php:8.4-apache

# Instalar dependencias del sistema y Composer
RUN apt-get update && apt-get install -y libpq-dev zip unzip git curl
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install pdo pdo_pgsql
RUN a2enmod rewrite

# Copiar todo el código del proyecto
COPY . /var/www/html

# Configurar Apache para que apunte a la carpeta public de Laravel
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# Instalar las dependencias de Composer dentro del contenedor
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permisos para storage y bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
