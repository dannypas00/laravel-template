FROM php:8.3-cli

ARG NODE_MAJOR=21
ARG USER=app

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN set-eux; apt update; apt install -y gpg wget curl; apt clean

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN set -eux; install-php-extensions \
    exif \
    soap \
    pcntl \
    intl \
    gmp \
    zip \
    pdo_mysql \
    sockets \
    gd \
    redis \
    xdebug \
    memcached


ARG USER=app

RUN set -eux; useradd -u 1000 -m ${USER};

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash -; \
    apt update; apt install -y nodejs; apt clean \

# Install xdebug and copy config
RUN docker-php-ext-enable xdebug
ADD xdebug.ini /usr/local/etc/php/conf.d/zz_xdebug.ini
RUN npm config --global set cache=/var/www/.npm
RUN (mkdir /var/www/.npm || true) && chown -R 1000:1000 /var/www/.npm


USER ${USER}

WORKDIR /app
