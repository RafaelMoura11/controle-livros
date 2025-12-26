#!/usr/bin/env sh
set -e

php artisan migrate --force

# evita cache "preso" no deploy
php artisan optimize:clear || true

exec /usr/bin/supervisord -c /etc/supervisord.conf
