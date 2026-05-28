FROM php:8.2-apache

# 1. Instala dependências do sistema e ferramentas necessárias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# 2. Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Ativa o mod_rewrite do Apache
RUN a2enmod rewrite

# 4. Copia todo o código do projeto para o servidor
COPY . /var/www/html/

# 5. Executa o Composer install
RUN if [ -f "/var/www/html/livrosViajantes/composer.json" ]; then \
        cd /var/www/html/livrosViajantes && composer install --no-dev --optimize-autoloader; \
    else \
        cd /var/www/html && composer install --no-dev --optimize-autoloader; \
    fi

# 6. Ajusta o Root do Apache dinamicamente
RUN if [ -d "/var/www/html/livrosViajantes/public" ]; then \
        ENV_ROOT="/var/www/html/livrosViajantes/public"; \
    else \
        ENV_ROOT="/var/www/html/public"; \
    fi && \
    sed -ri -e "s!/var/www/html!${ENV_ROOT}!g" /etc/apache2/sites-available/*.conf && \
    sed -ri -e "s!/var/www/html!${ENV_ROOT}!g" /etc/apache2/apache2.conf

FROM php:8.2-apache

# 1. Instala dependências do sistema e ferramentas necessárias
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# 2. Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Ativa o mod_rewrite do Apache
RUN a2enmod rewrite

# 4. Copia todo o código do projeto para o servidor
COPY . /var/www/html/

# 5. Executa o Composer install
RUN if [ -f "/var/www/html/livrosViajantes/composer.json" ]; then \
        cd /var/www/html/livrosViajantes && composer install --no-dev --optimize-autoloader; \
    else \
        cd /var/www/html && composer install --no-dev --optimize-autoloader; \
    fi

# 6. Ajusta o Root do Apache dinamicamente
RUN if [ -d "/var/www/html/livrosViajantes/public" ]; then \
        ENV_ROOT="/var/www/html/livrosViajantes/public"; \
    else \
        ENV_ROOT="/var/www/html/public"; \
    fi && \
    sed -ri -e "s!/var/www/html!${ENV_ROOT}!g" /etc/apache2/sites-available/*.conf && \
    sed -ri -e "s!/var/www/html!${ENV_ROOT}!g" /etc/apache2/apache2.conf

# 7. Cria o arquivo .env físico em tempo de execução de forma direta (Sem colchetes)
CMD if [ -d "/var/www/html/livrosViajantes" ]; then TARGET="/var/www/html/livrosViajantes/.env"; else TARGET="/var/www/html/.env"; fi; \
    echo "DB_HOST=\"$DB_HOST\"" > $TARGET && \
    echo "DB_PORT=\"$DB_PORT\"" >> $TARGET && \
    echo "DB_NAME=\"$DB_NAME\"" >> $TARGET && \
    echo "DB_USER=\"$DB_USER\"" >> $TARGET && \
    echo "DB_PASS=\"$DB_PASS\"" >> $TARGET && \
    echo "APP_ENV=\"$APP_ENV\"" >> $TARGET && \
    echo "JWT_SECRET=\"$JWT_SECRET\"" >> $TARGET && \
    chown www-data:www-data $TARGET && \
    apache2-foreground

# 8. Dá as permissões necessárias para o restante do servidor
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80