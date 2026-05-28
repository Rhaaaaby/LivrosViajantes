FROM php:8.2-apache

# 1. Instala dependências do sistema e ferramentas necessárias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# 2. Instala o Composer globalmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Ativa o mod_rewrite do Apache
RUN a2enmod rewrite

# 4. O PULO DO GATO: Força o Apache a injetar as variáveis de ambiente no PHP
RUN echo "Listen 80" > /etc/apache2/ports.conf && \
    echo "PassEnv DB_HOST DB_PORT DB_NAME DB_USER DB_PASS APP_ENV JWT_SECRET" >> /etc/apache2/apache2.conf

# 5. Copia todo o código do projeto para o servidor
COPY . /var/www/html/

# 6. Executa o Composer install
RUN if [ -f "/var/www/html/livrosViajantes/composer.json" ]; then \
        cd /var/www/html/livrosViajantes && composer install --no-dev --optimize-autoloader; \
    else \
        cd /var/www/html && composer install --no-dev --optimize-autoloader; \
    fi

# 7. Ajusta o Root do Apache dinamicamente
RUN if [ -d "/var/www/html/livrosViajantes/public" ]; then \
        ENV_ROOT="/var/www/html/livrosViajantes/public"; \
    else \
        ENV_ROOT="/var/www/html/public"; \
    fi && \
    sed -ri -e "s!/var/www/html!${ENV_ROOT}!g" /etc/apache2/sites-available/*.conf && \
    sed -ri -e "s!/var/www/html!${ENV_ROOT}!g" /etc/apache2/apache2.conf

# 8. Dá as permissões necessárias para o servidor
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80