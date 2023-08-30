ARG PHP_VERSION='8.2'

# ================
# Base Stage
# ================
FROM serversideup/php:${PHP_VERSION}-fpm-nginx as base
ENV AUTORUN_ENABLED=false
# ENV SSL_MODE=off

# ================
# Production Stage
# ================
# FROM base as production

# ENV APP_ENV=production
# ENV APP_DEBUG=false

# Required Modules
RUN apt-get update && \
    apt-get install -y php${PHP_VERSION}-mysql php${PHP_VERSION}-xml php${PHP_VERSION}-curl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

USER $PUID:$PGID

# Copy contents.
# - To ignore files or folders, use .dockerignore
COPY --chown=$PUID:$PGID . .

RUN composer install --optimize-autoloader --no-dev --no-interaction --no-progress --ansi

# artisan commands
RUN php ./artisan optimze

USER root:root