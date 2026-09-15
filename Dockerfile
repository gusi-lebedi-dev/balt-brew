FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache nginx supervisor curl \
    && mkdir -p /run/nginx /var/log/supervisor

COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions mysqli opcache gd intl zip exif

COPY .docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY .docker/php/php.ini /usr/local/etc/php/conf.d/balt-brew.ini
COPY .docker/supervisord.conf /etc/supervisord.conf
COPY . /var/www/html

RUN mkdir -p /var/www/html/wp-content/uploads \
    && chown -R www-data:www-data /var/www/html/wp-content/uploads

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
    CMD curl --fail --silent http://127.0.0.1/healthz || exit 1

ENTRYPOINT ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]
