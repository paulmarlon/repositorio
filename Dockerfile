FROM php:8.2-apache
RUN apt-get update && apt-get install -y libpq-dev zip unzip git
RUN docker-php-ext-install pdo pdo_pgsql
RUN a2enmod rewrite
COPY . /var/www/html
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
EXPOSE 80
