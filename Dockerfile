FROM php:8.2-apache

# Install curl extension (required for proxy support)
RUN docker-php-ext-install curl

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Configure PHP settings for longer execution time
RUN echo "max_execution_time = 120" >> /usr/local/etc/php/php.ini && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/php.ini

# Set working directory
WORKDIR /var/www/html

# Copy PHP files
COPY --chown=www-data:www-data api.php ./
COPY --chown=www-data:www-data shop.php ./

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]
