# ---------------------------------------------------------------
# Stage 1: Build frontend assets (Vite + Tailwind)
# ---------------------------------------------------------------
FROM node:20-alpine AS frontend
WORKDIR /app

COPY package.json package-lock.json* ./
RUN npm ci --no-audit --no-fund

COPY . .
RUN npm run build

# ---------------------------------------------------------------
# Stage 2: PHP 8.3 + Apache (production runtime)
# ---------------------------------------------------------------
FROM php:8.3-apache

# Install PHP extensions & system tools
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql zip bcmath opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apache config pointing DocumentRoot to /var/www/html/public
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Copy app source
COPY . .

# Remove build artifacts from host (if any) so they don't leak into image
RUN rm -rf public/build node_modules vendor bootstrap/cache/services.php bootstrap/cache/packages.php

# Install PHP dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy built assets from frontend stage
COPY --from=frontend /app/public/build ./public/build

# Ensure writable dirs exist
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public/build \
    && chmod -R 775 storage bootstrap/cache public/build

# Entrypoint runs migrations at container start (DB env vars available then)
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
