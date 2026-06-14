FROM alpine:3.23
# Setup document root
WORKDIR /var/www/main

# Install packages and remove default server definition
RUN apk update && apk upgrade && apk add --no-cache \
    curl \
    mariadb-client \
    nginx \
    php85 \
    php85-ctype \
    php85-curl \
    php85-dom \
    php85-fileinfo \
    php85-fpm \
    php85-gd \
    php85-gettext \
    php85-iconv \
    php85-intl \
    php85-mbstring \
    php85-mysqli \
    php85-openssl \
    php85-pdo \
    php85-pdo_mysql \
    php85-pdo_sqlite \
    php85-sqlite3 \
    php85-phar \
    php85-session \
    php85-simplexml \
    php85-sodium \
    php85-tokenizer \
    php85-xml \
    php85-xmlreader \
    php85-xmlwriter \
    php85-zip \
    supervisor

# Config PHP
RUN sed -i "s/user = nobody/user = root/g" /etc/php85/php-fpm.d/www.conf \
    && sed -i "s/group = nobody/group = root/g" /etc/php85/php-fpm.d/www.conf \
    && sed -i "s/listen.owner = nobody/listen.owner = root/g" /etc/php85/php-fpm.d/www.conf \
    && sed -i "s/listen.group = nobody/listen.group = root/g" /etc/php85/php-fpm.d/www.conf

RUN ln -s /usr/bin/php85 /usr/bin/php

RUN curl -sS https://getcomposer.org/installer | php85 -- --install-dir=/usr/bin --filename=composer

# Configure nginx
COPY config/nginx.conf /etc/nginx/nginx.conf
COPY config/conf.d /etc/nginx/conf.d/
COPY config/ssl /etc/nginx/ssl

# Configure PHP-FPM
COPY config/fpm-pool.conf /etc/php85/php-fpm.d/www.conf
COPY config/php.ini /etc/php8/conf.d/custom.ini

# Configure supervisord
COPY config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Set ENV
ENV ENV=/etc/profile
# Add command aliases for shell
COPY config/aliases.sh /etc/profile.d/aliases.sh

# Copy scripts and make them executable
#COPY shell-scripts/clear-logs.sh /bin/clear-logs
#RUN chown www-data:www-data /bin/clear-logs && chmod +x /bin/clear-logs

# Create www-data user -- for some reason, the group already existed
# We use UID 1000 to match the host user (so volume permissions automatically line up).
#   I don't like this, it feels like it shouldn't be something hardcoded, but I haven't found a better option
RUN adduser -D -H -u 1000 -h /var/www/main -s /bin/bash www-data -G www-data

# Make sure files/folders needed by the processes are accessable when they run under the nobody user
RUN chown -R www-data:www-data /var/www/main /run /var/lib/nginx /var/log/nginx /var/log/php85 /etc/nginx/ssl

# Add application
COPY --chown=www-data:www-data . /var/www/main

# Expose the port nginx is reachable on
EXPOSE 80
EXPOSE 443

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Run supervisor as www-data so nginx and such are spawned by www-data, too.
USER www-data

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1/fpm-ping
