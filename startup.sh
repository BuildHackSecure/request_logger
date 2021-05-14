#!/bin/bash

echo $2 > /var/www/domain.txt
service nginx start
service php7.4-fpm start
ruby /var/www/dns.rb $1