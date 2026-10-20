# syntax=docker/dockerfile:1

FROM composer:2.8 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN --mount=type=secret,id=composer_auth,dst=/run/secrets/composer_auth \
    COMPOSER_AUTH="$(cat /run/secrets/composer_auth)" \
    composer install --no-dev --no-interaction --no-progress --prefer-dist --no-scripts --optimize-autoloader


FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY --from=vendor /app/vendor ./vendor
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build


FROM php:8.4-fpm-alpine AS runtime

WORKDIR /app

RUN apk add --no-cache nginx gettext libzip-dev libxml2-dev sqlite-dev \
    && docker-php-ext-install pdo_sqlite zip dom \
    && mkdir -p /run/nginx /var/lib/nginx /tmp/nginx

COPY --from=vendor /app/vendor ./vendor
COPY . .
COPY --from=assets /app/public/build ./public/build
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf.template /etc/nginx/http.d/default.conf.template
COPY docker/start-container /usr/local/bin/start-container
# ANPR's WAF rejects Render's network, so import the copies the weekly
# workflow mirrors to the anpr-sources release. ADD re-checks remote
# content on every build, so a changed mirror invalidates the cache.
ADD https://github.com/robertogallea/laravel-codicefiscale-showcase/releases/download/anpr-sources/ANPR_archivio_comuni.csv \
    https://github.com/robertogallea/laravel-codicefiscale-showcase/releases/download/anpr-sources/tabella_2_statiesteri.xlsx \
    /tmp/anpr/

RUN chmod +x /usr/local/bin/start-container \
    && mkdir -p storage/app/codicefiscale storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && touch database/database.sqlite \
    && APP_ENV=production APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= DB_CONNECTION=sqlite DB_DATABASE=/app/database/database.sqlite php artisan package:discover --ansi \
    && APP_ENV=production APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= DB_CONNECTION=sqlite DB_DATABASE=/app/database/database.sqlite php artisan migrate --force \
    && APP_ENV=production APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= DB_CONNECTION=sqlite DB_DATABASE=/app/database/database.sqlite php artisan db:seed --class=Database\\Seeders\\ProductionDemoSeeder --force \
    && APP_ENV=production APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= DB_CONNECTION=sqlite DB_DATABASE=/app/database/database.sqlite php artisan codice-fiscale:import-places /tmp/anpr/ANPR_archivio_comuni.csv /tmp/anpr/tabella_2_statiesteri.xlsx \
    && rm -rf /tmp/anpr \
    && chown -R www-data:www-data bootstrap/cache database storage

ENV APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=sqlite \
    DB_DATABASE=/app/database/database.sqlite \
    LOG_CHANNEL=stderr \
    PORT=10000

USER www-data

EXPOSE 10000

CMD ["start-container"]
