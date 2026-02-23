FROM php:8.2-apache

# Disable default mpm and use mpm_prefork
RUN a2dismod mpm_event && a2enmod mpm_prefork rewrite

# Configure PHP settings
RUN echo "max_execution_time = 120" >> /usr/local/etc/php/php.ini && \
    echo "memory_limit = 256M" >> /usr/local/etc/php/php.ini

# Set working directory
WORKDIR /var/www/html

# Copy API file
COPY --chown=www-data:www-data api.php ./

# Expose port 80
EXPOSE 80

CMD ["apache2-foreground"]
