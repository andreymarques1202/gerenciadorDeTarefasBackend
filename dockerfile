FROM php:8.2-apache

# Instala dependências
RUN apt-get update && apt-get install -y \
    libzip-dev libonig-dev libxml2-dev \
    zip unzip git curl libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Habilita mod_rewrite do Apache
RUN a2enmod rewrite

# Aponta o DocumentRoot para o diretório public do Laravel
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia o projeto
COPY . .

# Instala dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissões e cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Gera chave e cache (vai falhar se não houver .env, mas tudo bem no Render)
RUN php artisan key:generate || true && \
    php artisan config:cache || true && \
    php artisan route:cache || true && \
    php artisan view:cache || true

# Expõe a porta 80
EXPOSE 80

# Inicia o Apache
CMD ["apache2-foreground"]
