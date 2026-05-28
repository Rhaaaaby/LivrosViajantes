FROM php:8.2-apache

# 1. Ativa o mod_rewrite do Apache (essencial para rotas/URL amigável)
RUN a2enmod rewrite

# 2. Instala a extensão do PostgreSQL para o PHP
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# 3. Copia TODO o conteúdo do repositório para o servidor
COPY . /var/www/html/

# 4. Ajusta o Root do Apache dinamicamente: se a pasta livrosViajantes/public existir, usa ela; se não, usa public/
RUN if [ -d "/var/www/html/livrosViajantes/public" ]; then \
        ENV_ROOT="/var/www/html/livrosViajantes/public"; \
    else \
        ENV_ROOT="/var/www/html/public"; \
    fi && \
    sed -ri -e "s!/var/www/html!${ENV_ROOT}!g" /etc/apache2/sites-available/*.conf && \
    sed -ri -e "s!/var/www/html!${ENV_ROOT}!g" /etc/apache2/apache2.conf

# 5. Aplica as permissões corretas para o servidor ler os arquivos
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80