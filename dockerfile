# Etapa base
FROM php:8.2-apache

# Instala dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git unzip zip curl libzip-dev libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilita o mod_rewrite do Apache
RUN a2enmod rewrite

# Ajusta o DocumentRoot para a pasta "public"
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Copia os arquivos da aplicação
COPY . /var/www/html/

# Define diretório padrão de trabalho
WORKDIR /var/www/html

# Permissões e instalação do Laravel
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    composer install --no-dev --optimize-autoloader && \
    cp .env.example .env && \
    touch /var/www/html/database/database.sqlite && \
    chmod -R 775 /var/www/html/database && \
    php artisan key:generate && \
    php artisan migrate --force && \
    php artisan config:cache && \
    chmod -R 775 storage bootstrap/cache


# Expõe a porta
EXPOSE 80
