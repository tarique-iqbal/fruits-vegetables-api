FROM php:8.2-fpm

# Set working directory inside container
WORKDIR /var/www/food

# Install dependencies
RUN apt-get update && apt-get install -y \
    git unzip libpq-dev libzip-dev zip libonig-dev curl \
    && docker-php-ext-install pdo pdo_mysql zip mbstring

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy custom PHP configuration
COPY .docker/php/symfony.ini /usr/local/etc/php/conf.d/
COPY .docker/php/xdebug.ini /usr/local/etc/php/conf.d/

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Copy Composer files first (to leverage Docker caching)
COPY composer.json composer.lock /var/www/food/

# Install dependencies (This step will be cached if files don't change)
RUN composer install --no-interaction --prefer-dist --no-scripts --no-progress

# Copy all application files
COPY . .

# Ensure permissions
RUN chown -R www-data:www-data /var/www/food

# Expose port for PHP-FPM
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
