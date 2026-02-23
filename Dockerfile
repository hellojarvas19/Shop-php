# Updated Dockerfile with full path for php-fpm

FROM php:8.4-fpm

# other instructions

# Update the startup script
CMD ["/usr/sbin/php-fpm84", "-D"]
