FROM php:8.2-apache

# Instala extensões necessárias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libsqlite3-dev \
    pkg-config \
    zip unzip git curl \
    && docker-php-ext-install pdo pdo_sqlite zip

# Habilita mod_rewrite do Apache
RUN a2enmod rewrite

# Copia o Composer do container oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia o código do projeto
COPY . /var/www/html/

# Define o diretório de trabalho
WORKDIR /var/www/html

# Permissões e dependências
RUN composer install --no-dev --optimize-autoloader && \
    chmod -R 755 /var/www/html && \
    chown -R www-data:www-data /var/www/html

# Cria o arquivo .env, configura chave e cache
RUN cp .env.example .env && \
    php artisan key:generate && \
    php artisan config:cache

# Expondo a porta do Apache
EXPOSE 80
