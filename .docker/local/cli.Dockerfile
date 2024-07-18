FROM php:8.3-cli

ARG NODE_MAJOR=21
ARG USER=app

# Install composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update && \
    apt-get install -y --force-yes --no-install-recommends curl wget gpg && \
    apt-get clean


RUN install-php-extensions \
	bcmath \
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
    memcached \
    imagick

# Install nodejs
RUN mkdir -p /etc/apt/keyrings && \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" > /etc/apt/sources.list.d/nodesource.list

RUN apt update && apt install -y nodejs && apt clean

RUN usermod -u 1000 ${USER}

USER ${USER}

WORKDIR /app

ENTRYPOINT ["bash"]
