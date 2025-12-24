FROM php:8.4-fpm-alpine

# Dependências do sistema
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

# Copia projeto
COPY . .

# Instala dependências PHP
RUN composer install --no-dev --optimize-autoloader

# Build assets
RUN npm install && npm run build

# Nginx config
COPY .render/nginx.conf /etc/nginx/nginx.conf

# Supervisor config
COPY .render/supervisord.conf /etc/supervisord.conf

# Permissões
RUN mkdir -p storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]
