# Set the base image to PHP 7.4 Apache
FROM php:7.4-apache

# Install required extensions for Laravel
RUN apt-get update && apt-get install -y \
    netcat \
    libzip-dev \
    zip \
    curl \
    && docker-php-ext-install zip pdo_mysql

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the contents of the app directory to the container
COPY . .

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set file permissions
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache

# Update the Apache virtual host configuration
RUN sed -i 's/\/var\/www\/html/\/var\/www\/html\/public/g' /etc/apache2/sites-available/000-default.conf
# Modify the max_upload_filesize directive in PHP
RUN echo "upload_max_filesize = 64M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 64M" >> /usr/local/etc/php/conf.d/uploads.ini

# Enable mod_rewrite for Apache
RUN a2enmod rewrite

# Install Composer and dependencies
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer update && composer install --no-interaction && \
    composer dump-autoload --optimize && \
    composer clear-cache

# Set environment variables
ENV APP_ENV=local
ENV APP_DEBUG=true
ENV APP_URL=http://localhost
ENV DB_HOST=db
ENV DB_PORT=3306
ENV DB_DATABASE=laravel
ENV DB_USERNAME=laravel
ENV DB_PASSWORD=laravel

RUN cp .env.example .env
RUN php artisan key:generate \
   && php artisan config:cache

# Expose port 80 and start Apache
EXPOSE 80
CMD ["bash", "/var/www/html/entrypoint.sh"]