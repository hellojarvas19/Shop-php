FROM alpine:latest
                                                                                        # Install PHP, Nginx and curl
RUN apk add --no-cache php php-fpm php-curl nginx
                                                                                        # Create nginx config file
RUN mkdir -p /var/www/localhost/htdocs

# Copy files
COPY --chown=nginx:nginx api.php /var/www/localhost/htdocs/

# Copy nginx config
COPY default.conf /etc/nginx/http.d/
                                                                                        # Create startup script
RUN echo '#!/bin/sh' > /start.sh && \
    echo 'php-fpm -D' >> /start.sh && \
    echo 'nginx -g "daemon off;"' >> /start.sh && \                                         chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
