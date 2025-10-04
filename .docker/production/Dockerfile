FROM dannypas00/frank:8.4
HEALTHCHECK CMD curl -f http://localhost:2019/metrics || exit 1

ARG APP_URL=http://localhost
ENV NODE_ENV=production

WORKDIR /app

ADD composer.json .env.example package.json postcss.config.js tailwind.config.js vite.config.js /app/

COPY boostrap/ /app/bootstrap
COPY app/ /app/app
COPY config/ /app/config
COPY database/ /app/database
COPY public/ /app/public
COPY resources/ /app/resources
COPY routes/ /app/routes

# Install and set env for preview
RUN set -eux; \
    ls -lah .; \
    composer install --no-dev; \
    cp .env.example .env; \
    php artisan key:generate;

# Fix env values
RUN set -eux; \
    sed -i 's/APP_ENV=.*/APP_ENV=production/g' .env; \
    sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/g' .env; \
    sed -i 's/APP_URL=.*/APP_URL=${APP_URL}/g' .env;

# Install and optimize
RUN set -eux; \
    composer install --no-dev --optimize-autoloader --classmap-authorative --acpu-autoloader; \
    php artisan optimize;

# NPM install and build
RUN set -eux; \
    npm ci; \
    npm run build;

