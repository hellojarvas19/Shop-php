FROM alpine:latest

# Install PHP, Nginx and curl
RUN apk add --no-cache php php-fpm php-curl nginx

# Configure PHP
RUN echo "max_execution_time = 120" >> /etc/php/php.ini && \
    echo "memory_limit = 256M" >> /etc/php/php.ini

# Configure Nginx
RUN echo "server {" >> /etc/nginx/http.d/default.conf && \
    echo "    listen 80;" >> /etc/nginx/http.d/default.conf && \
    echo "    server_name _;" >> /etc/nginx/http.d/default.conf && \                        echo "    root /var/www/localhost/htdocs;" >> /etc/nginx/http.d/default.conf && \
    echo "    index index.php;" >> /etc/nginx/http.d/default.conf && \                      echo "    location / {" >> /etc/nginx/http.d/default.conf && \
    echo "        try_files \$uri \$uri/ /index.php?\$query_string;" >> /etc/nginx/http.d/default.conf && \
    echo "    }" >> /etc/nginx/http.d/default.conf && \
    echo "    location ~ \.php$ {" >> /etc/nginx/http.d/default.conf && \
    echo "        fastcgi_pass 127.0.0.1:9000;" >> /etc/nginx/http.d/default.conf && \
    echo "        fastcgi_index index.php;" >> /etc/nginx/http.d/default.conf && \
    echo "        fastcgi_param SCRIPT_FILENAME /var/www/localhost/htdocs\$fastcgi_script_name;" >> /etc/nginx/http.d/default.conf && \
    echo "        include fastcgi_params;" >> /etc/nginx/http.d/default.conf && \
    echo "    }" >> /etc/nginx/http.d/default.conf && \
    echo "}" >> /etc/nginx/http.d/default.conf

# Create web directory and copy files
RUN mkdir -p /var/www/localhost/htdocs
COPY --chown=nginx:nginx api.php /var/www/localhost/htdocs/

# Create startup script
RUN echo "#!/bin/sh" > /start.sh && \
    echo "php-fpm -D" >> /start.sh && \
    echo "nginx -g 'daemon off;'" >> /start.sh && \
    chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
