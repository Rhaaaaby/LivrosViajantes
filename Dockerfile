FROM php:8.2-apache

# Ativa o mod_rewrite do Apache (essencial para rotas amigáveis)
RUN a2enmod rewrite

# Instala a extensão PDO PostgreSQL para o PHP conversar com o Neon
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Copia os arquivos do seu projeto para a pasta do servidor
COPY . /var/www/html/

# Garante as permissões corretas para o Apache ler os arquivos
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80