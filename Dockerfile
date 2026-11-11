# ---------- Estágio 1: build do front ----------
FROM node:22-alpine AS frontend
WORKDIR /app
COPY front/package*.json ./
RUN npm install
COPY front/ ./
RUN npm run build

# ---------- Estágio 2: dependências da API ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY api/ ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ---------- Estágio 3: imagem final (PHP-FPM + Nginx) ----------
FROM php:8.4-fpm-trixie AS final

# Instala Nginx e extensões necessárias para o Laravel
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        libzip-dev \
    && docker-php-ext-install pdo_mysql zip bcmath opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Código da API (com vendor já instalado)
WORKDIR /var/www/api
COPY api/ ./
COPY --from=vendor /app/vendor ./vendor

# Front compilado
COPY --from=frontend /app/dist /usr/share/nginx/html

# Certificado CA do TiDB Cloud
COPY docker/ca.pem /etc/ssl/certs/ca.pem

# Configuração do Nginx e script de entrada
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Permissões de storage/cache do Laravel
RUN chown -R www-data:www-data /var/www/api/storage /var/www/api/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]