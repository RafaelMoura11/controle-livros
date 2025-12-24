FROM php:8.4-fpm-alpine

# Dependências do sistema + extensões PHP
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    postgresql-dev \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_pgsql intl mbstring zip opcache

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copia o projeto
COPY . .

# Configurações do Composer (Flux Pro) via secret
ARG COMPOSER_AUTH
ENV COMPOSER_AUTH=$COMPOSER_AUTH

# Instala dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Build assets
RUN npm install && npm run build

# Configs do Nginx/Supervisor
COPY .render/nginx.conf /etc/nginx/nginx.conf
COPY .render/supervisord.conf /etc/supervisord.conf

# Script de start (migrate + start services)
RUN chmod +x /var/www/html/start.sh

# Permissões (importante pra logs/cache)
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

EXPOSE 10000

CMD ["/var/www/html/start.sh"]
