FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    zip

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_pgsql \
    pgsql \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project first
COPY . .

# Install dependencies FIRST
RUN composer install --no-dev --optimize-autoloader

# Now vendor exists → artisan works
RUN php artisan optimize:clear || true
RUN rm -rf bootstrap/cache/*

EXPOSE 10000

# Use ONE CMD only
CMD sh -c "php artisan serve --host=0.0.0.0 --port=10000"
