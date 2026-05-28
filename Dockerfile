FROM php:8.2-apache

# 1. Instala dependências do sistema e ferramentas necessárias para o Composer e PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# 2. Instala o Composer globalmente no container
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Ativa o mod_rewrite do Apache
RUN a2enmod rewrite

# 4. Copia todo o código do projeto para o servidor
COPY . /var/www/html/

# 5. Executa o Composer install para gerar a pasta vendor/ automática na nuvem
# Ele vai procurar onde está o seu arquivo composer.json automaticamente
RUN if [ -f "/var/www/html/livrosViajantes/composer.json" ]; then \
        cd /var/www/html/livrosViajantes && composer install --no-dev --optimize-autoloader; \
    else \
        cd /var/www/html && composer install --no-dev --optimize-autoloader; \
    fi

# 6. Ajusta o Root do Apache dinamicamente (raiz ou subpasta)
RUN if [ -d "/var/www/html/livrosViajantes/public" ]; then \
        ENV_ROOT="/var/www/html/livrosViajantes/public"; \
    else \
        ENV_ROOT="/var/www/html/public"; \
    fi && \
    sed -ri -e "s!/var/www/html!${ENV_ROOT}!g" /etc/apache2/sites-available/*.conf && \
    sed -ri -e "s!/var/www/html!${ENV_ROOT}!g" /etc/apache2/apache2.conf

# 7. Dá as permissões necessárias para o servidor ler os arquivos e a pasta vendor
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80