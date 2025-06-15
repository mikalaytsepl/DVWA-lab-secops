FROM php:8.3-apache-bookworm

LABEL org.opencontainers.image.source=https://github.com/digininja/DVWA
LABEL org.opencontainers.image.description="DVWA pre-built image."
LABEL org.opencontainers.image.licenses="gpl-3.0"

WORKDIR /var/www/html

# Dodanie repozytorium sid tylko dla libxml2 i zlib1g
RUN echo "deb http://deb.debian.org/debian sid main" > /etc/apt/sources.list.d/unstable.list \
 && echo -e "Package: libxml2\nPin: release a=sid\nPin-Priority: 500\nPackage: zlib1g\nPin: release a=sid\nPin-Priority: 500\nPackage: zlib1g-dev\nPin: release a=sid\nPin-Priority: 500" \
    > /etc/apt/preferences.d/libxml2-zlib-pinning \
 && apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y libxml2 zlib1g zlib1g-dev \
 && apt-get clean && rm -rf /var/lib/apt/lists/*


# Instalacja zależności i rozszerzeń PHP
RUN apt-get update \
 && export DEBIAN_FRONTEND=noninteractive \
 && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev iputils-ping git \
 && docker-php-ext-configure gd --with-jpeg --with-freetype \
 && docker-php-ext-install gd mysqli pdo pdo_mysql \
 && a2enmod rewrite \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Pliki aplikacji
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data config/config.inc.php.dist config/config.inc.php

# Instalacja zależności API
RUN cd /var/www/html/vulnerabilities/api && composer install
