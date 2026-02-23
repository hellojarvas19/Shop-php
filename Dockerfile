FROM alpine:latest

# Install PHP, Nginx and curl                                                           RUN apk add --no-cache php php-fpm php-curl nginx

# Remove default nginx config                                                           RUN rm -f /etc/nginx/http.d/default.conf

# Create nginx config                                                                   RUN printf '%s\n' \
    'server {' \
    '    listen 80;' \
    '    server_name _;' \
    '    root /var/www/localhost/htdocs;' \
    '    index index.php;' \
    '    location / {' \
    '        try_files $uri $uri/ /index.php?$query_string;' \
    '    }' \                                                                               '    location ~ \.php$ {' \
    '        fastcgi_pass 127.0.0.1:9000;' \
    '        fastcgi_index index.php;' \
    '        fastcgi_param SCRIPT_FILENAME /var/www/localhost/htdocs$fastcgi_script_name;' \
    '        include fastcgi_params;' \
    '    }' \
    '}' > /etc/nginx/http.d/default.conf

# Create web directory
RUN mkdir -p /var/www/localhost/htdocs

# Copy files
COPY --chown=nginx:nginx api.php /var/www/localhost/htdocs/

# Create startup script
RUN printf '%s\n' \
    '#!/bin/sh' \
    'php-fpm -D' \
    'nginx -g "daemon off;"' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
