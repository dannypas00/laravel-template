FROM dannypas00/laravel-fpm:8.2

USER 0

# Install xdebug and copy config
RUN docker-php-ext-enable xdebug
ADD xdebug.ini /usr/local/etc/php/conf.d/zz_xdebug.ini
RUN npm config --global set cache=/var/www/.npm
RUN (mkdir /var/www/.npm || true) && chown -R 1000:1000 /var/www/.npm

USER 1000
