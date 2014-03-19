Installation Instructions for Ubuntu 12.04
==========================================

### Install required packages

    # Install updated package repos for nodejs and redis
    sudo apt-add-repository ppa:chris-lea/redis-server
    sudo apt-add-repository ppa:chris-lea/node.js

    # Update package cache
    sudo apt-get update

    # Install Core Packages
    sudo apt-get install build-essential \
                         apache2 \
                         curl \
                         git \
                         git-core \
                         libapache2-mod-php5 \
                         libevent-dev \
                         libxslt1-dev \
                         mysql-server \
                         nodejs \
                         openjdk-6-jre \
                         php5 \
                         php5-curl \
                         php5-gd \
                         php5-mcrypt \
                         php5-mysql \
                         php5-cli \
                         python-dev \
                         python-qt4 \
                         python-setuptools \
                         python-virtualenv \
                         rabbitmq-server \
                         redis-server \
                         vim \
                         xvfb \
                         zip \
                         zlib1g-dev

    # Download and Install Elasticsearch
    wget https://download.elasticsearch.org/elasticsearch/elasticsearch/elasticsearch-1.0.1.deb
    sudo dpkg -i elasticsearch-1.0.1.deb

    # Download and Install composer
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer

    # Enable required apache modules
    sudo a2enmod deflate rewrite
