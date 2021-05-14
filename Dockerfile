FROM ubuntu:20.04

ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get -qq update -y && \
    apt-get -y -qq install \
    ruby \
    nginx \
    php-fpm && \
    echo "Packages Installed"

COPY app /var/www/

RUN chown -R www-data:www-data /var/www

ADD default /etc/nginx/sites-available/default

COPY startup.sh /startup.sh
RUN chmod +x /startup.sh

EXPOSE 80 53
