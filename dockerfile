# Etapa base
FROM php:8.2-apache

# Instala dependências
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html/

WORKDIR /var/www/html

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Comandos separados para debug
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html
RUN composer install --no-dev --optimize-autoloader
RUN cp .env.example .env
RUN touch /var/www/html/database/database.sqlite
RUN chown -R www-data:www-data /var/www/html/database
RUN chmod -R 775 /var/www/html/database
RUN php artisan key:generate
RUN php artisan migrate --force
RUN php artisan config:cache
RUN chmod -R 775 storage bootstrap/cache

EXPOSE 80
