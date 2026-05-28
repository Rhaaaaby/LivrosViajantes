FROM php:8.2-apache

# 1. Configura o Apache para apontar para a pasta public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 2. Ativa o mod_rewrite do Apache (essencial para rotas/URL amigável)
RUN a2enmod rewrite

# 3. Instala a extensão do PostgreSQL para o PHP
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# 4. Copia o projeto e aplica as permissões corretas
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80