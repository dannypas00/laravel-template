FROM dannypas00/frank
ADD .docker/local/xdebug.ini /usr/local/etc/php/conf.d/zz_xdebug.ini
HEALTHCHECK CMD curl -f http://localhost:2019/metrics || exit 1
