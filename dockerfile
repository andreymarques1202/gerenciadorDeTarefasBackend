FROM php:8.2-apache

# Instala extensões necessárias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libsqlite3-dev \
    pkg-config \
    zip unzip git curl \
    && docker-php-ext-install pdo pdo_sqlite zip
# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia o projeto
COPY . /var/www/html/

# Define diretório padrão
WORKDIR /var/www/html

# Permissões e dependências
RUN composer install --no-dev --optimize-autoloader && \
    chmod -R 755 /var/www/html && \
    chown -R www-data:www-data /var/www/html

# Porta do Apache
EXPOSE 80
