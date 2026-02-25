FROM alpine:latest

# Install PHP, Nginx and curl
RUN apk add --no-cache php84 php84-fpm php-curl nginx

# Create web directory
RUN mkdir -p /var/www/localhost/htdocs

# Copy files
COPY --chown=nginx:nginx api.php /var/www/localhost/htdocs/
COPY --chown=nginx:nginx shop.txt /var/www/localhost/htdocs/
COPY default.conf /etc/nginx/http.d/

# Create startup script with full path to php-fpm
RUN echo -e "#!/bin/sh\n/usr/sbin/php-fpm84 -D\nnginx -g 'daemon off;'" > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
