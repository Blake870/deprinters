#!/usr/bin/env bash

sudo chmod -R 0777 /usr/local/bin

sudo LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
sudo apt-get update

sudo apt-get install -y redis-server redis-tools

sudo apt-get install -y \
    php7.2 \
    php7.2-dev \
    php7.2-fpm \
    php7.2-cli \
    php7.2-common \
    php7.2-json \
    php7.2-opcache \
    php7.2-mysql \
    php7.2-mbstring \
    php7.2-gd \
    php7.2-imap \
    php7.2-snmp \
    php7.2-gd \
    php7.2-curl \
    php7.2-zip \
    php7.2-xml \
    php7.2-intl \
    php7.2-soap \
    php7.2-xsl \
    php7.2-bcmath \
    php7.2-iconv \
    php-xdebug

sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password root"
sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password root"
sudo apt-get -y install mysql-server mysql-client

sudo mkdir -m0777 /home/vagrant/.composer/

sudo cp /media/etc/auth.json /home/vagrant/.composer/auth.json
sudo chown vagrant:vagrant /home/vagrant/.composer/auth.json

sudo apt-get install -y nginx
sudo apt-get install -y ssl-cert

sudo rm /etc/nginx/sites-enabled/default
sudo cp /media/etc/nginx/homegrown.conf /etc/nginx/sites-enabled/homegrown.conf


if [ ! -f "/tmp/composer.phar" ]
then
    cd /tmp && wget https://getcomposer.org/composer.phar
fi

sudo mv /tmp/composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
sudo chown vagrant:vagrant /usr/local/bin/composer

sudo chmod 777 /media/src/bin/magento

cp /media/src/app/etc/env.php.sample /media/src/app/etc/env.php

sudo service nginx restart

# echo "CREATE DATABASE hgrowndb" | mysql -uroot --password=""
# ... load dump ...
# cd /media/src && composer install
# cd /media/src && bin/magento deploy:mode:set developer




