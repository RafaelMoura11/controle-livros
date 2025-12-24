#!/usr/bin/env sh
set -e

# Se der erro, o deploy falha e você vê o log
php artisan migrate --force

# Opcional (bom em produção)
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

exec /usr/bin/supervisord -c /etc/supervisord.conf
