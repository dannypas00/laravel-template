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

RUN set -eux; \
	# Use "adduser -D ${USER}" for alpine based distros
	useradd -u 1000 -m ${USER}; \
	# Add additional capability to bind to port 80 and 443
	setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp; \
	# Give write access to /data/caddy and /config/caddy
	chown -R 1000:1000 /data/caddy && chown -R 1000:1000 /config/caddy

USER ${USER}

RUN echo $SHELL

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
