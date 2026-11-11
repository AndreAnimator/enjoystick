#!/bin/bash
set -e

# Substitui a porta 80 pela porta fornecida pelo Render (padrão 10000)
sed -i "s/listen 80;/listen ${PORT:-80};/" /etc/nginx/conf.d/default.conf

# Executa as migrations no banco TiDB (idempotente)
php artisan migrate --force

# Opcional: caches de produção (recomendado)
php artisan config:cache
php artisan route:cache

# Inicia o PHP-FPM em background
php-fpm -D

# Inicia o Nginx em foreground (mantém o container vivo)
nginx -g 'daemon off;'