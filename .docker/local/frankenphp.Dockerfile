FROM dunglas/frankenphp

RUN set-eux; apt update; apt install -y gpg wget curl; apt clean

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
ARG CUSTOM_USER_ID=1000

RUN set -eux; \
	# Use "adduser -D ${USER}" for alpine based distros
	useradd -u ${CUSTOM_USER_ID} -m ${USER}; \
	# Add additional capability to bind to port 80 and 443
	setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
	# Give write access to /data/caddy and /config/caddy
	chown -R ${CUSTOM_USER_ID}:${CUSTOM_USER_ID} /data/caddy && chown -R ${CUSTOM_USER_ID}:${CUSTOM_USER_ID} /config/caddy

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash -; \
    apt update; apt install -y nodejs; apt clean

# Install xdebug and copy config
RUN docker-php-ext-enable xdebug
ADD .docker/local/xdebug.ini /usr/local/etc/php/conf.d/zz_xdebug.ini
RUN npm config --global set cache=/var/www/.npm
RUN (mkdir /var/www/.npm || true) && chown -R ${CUSTOM_USER_ID}:${CUSTOM_USER_ID} /var/www/.npm

HEALTHCHECK NONE

USER ${USER}
