# ...

# Copia o projeto
COPY . /var/www/html/

# Define o diretório de trabalho como a pasta 'public'
WORKDIR /var/www/html

# Aponta o Apache para a pasta 'public'
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Habilita mod_rewrite e reinicia Apache
RUN a2enmod rewrite

# Permissões e dependências
RUN composer install --no-dev --optimize-autoloader && \
    chmod -R 755 /var/www/html && \
    chown -R www-data:www-data /var/www/html

# Gera a chave e configura cache
RUN cp .env.example .env && \
    php artisan key:generate && \
    php artisan config:cache

# Expondo a porta do Apache
EXPOSE 80
