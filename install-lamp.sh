#!/bin/bash

# Update Indices
sudo apt-get update

# Apache 2.4
sudo apt-get -y install apache2

# PHP 7.1
sudo apt-get -y remove php*
sudo apt-get -y install php7.1 php7.1-cli php7.1-common libapache2-mod-php7.1 php7.1-mysql php7.1-fpm php7.1-curl php7.1-gd php7.1-bz2 php7.1-mcrypt php7.1-json php7.1-tidy php7.1-mbstring php-redis php-memcached

sudo phpenmod mcrypt
sudo phpenmod curl

sudo a2enmod rewrite
sudo a2enmod headers

sudo service apache2 restart

# MySQL 5.7.18
wget https://dev.mysql.com/get/mysql-apt-config_0.8.6-1_all.deb # Grab the latest MySQL .deb from here: https://dev.mysql.com/downloads/repo/apt/
sudo dpkg -i mysql-apt-config*
sudo apt-get update
rm mysql-apt-config*

sudo apt-get install mysql-server
mysql_secure_installation

# phpMyAdmin
sudo add-apt-repository -y ppa:nijel/phpmyadmin
sudo apt-get update
sudo apt-get -y install phpmyadmin php-mbstring php-gettext
sudo phpenmod mcrypt
sudo phpenmod mbstring
sudo systemctl restart apache2

php -v
apachectl --version
mysql --version
php -r 'echo "\nIt means your PHP installation is working fine.\n";'

sudo apt-get clean
