FROM php:8.3-fpm

# -----------------------------------------------------------
# 1. Mise à jour des dépôts APT
# -----------------------------------------------------------
RUN set -eux; \
    apt-get update

# -----------------------------------------------------------
# 2. Installation des dépendances système
# -----------------------------------------------------------
RUN set -eux; \
    apt-get install -y --no-install-recommends \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libxml2-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    libmcrypt-dev \
    libpq-dev \
    libicu-dev \
    unzip \
    git \
    rsync \
    wget \
    xfonts-75dpi \
    xfonts-base \
    redis-tools && \
    rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------
# 3. Extension Intl
# -----------------------------------------------------------
RUN set -eux; \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl

# -----------------------------------------------------------
# 4. Extension GD
# -----------------------------------------------------------
RUN set -eux; \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# -----------------------------------------------------------
# 5. Extensions PHP principales
# -----------------------------------------------------------
RUN set -eux; \
    docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    xml \
    mbstring \
    opcache \
    bcmath


# -----------------------------------------------------------
# Xdebug (dev)
# -----------------------------------------------------------
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Configure Xdebug
COPY ./iac/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# -----------------------------------------------------------
# Redis extension (+ igbinary for better perf)
# -----------------------------------------------------------
RUN set -eux; \
    pecl install igbinary; \
    docker-php-ext-enable igbinary; \
    pecl install redis; \
    docker-php-ext-enable redis

# -----------------------------------------------------------
# Your PHP config overrides
# -----------------------------------------------------------
COPY iac/php/php.ini /usr/local/etc/php/conf.d/99-cleo.ini
COPY ./iac/etc/php /usr/local/etc/php

# -----------------------------------------------------------
# Composer
# -----------------------------------------------------------
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    ca-certificates \
    && update-ca-certificates

# Créer un preload minimal pour éviter le crash PHP pendant le build
RUN mkdir -p /var/www/config && echo "<?php // preload placeholder ?>" > /var/www/config/preload.php

# Installer Composer proprement
RUN set -eux; \
    curl -sS https://getcomposer.org/installer -o composer-setup.php; \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer; \
    rm composer-setup.php

# Vérification
RUN composer --version

# -----------------------------------------------------------
# wkhtmltopdf (Bookworm package works fine on Debian Trixie base)
# -----------------------------------------------------------
RUN set -eux; \
    wget -q https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/wkhtmltox_0.12.6.1-3.bookworm_amd64.deb; \
    apt-get install -y ./wkhtmltox_0.12.6.1-3.bookworm_amd64.deb; \
    rm ./wkhtmltox_0.12.6.1-3.bookworm_amd64.deb

# -----------------------------------------------------------
# APCu extension (cache local)
# -----------------------------------------------------------
RUN pecl install apcu \
    && docker-php-ext-enable apcu

# (optional) tune APCu
RUN { \
    echo "apc.enable_cli=1"; \
    echo "apc.shm_size=128M"; \
    } > /usr/local/etc/php/conf.d/99-apcu.ini

# -----------------------------------------------------------
# App setup
# -----------------------------------------------------------
WORKDIR /srv/app
COPY . .

# Installer les dépendances PHP (sans dev en prod)
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --prefer-dist --optimize-autoloader

# Créer les répertoires nécessaires et donner les bons droits
RUN mkdir -p var/cache var/log var/sessions \
    && chmod -R 777 var/cache var/log var/sessions

# DB env bootstrap
RUN chmod +x ./iac/scripts/set_db_env.sh \
    && ./iac/scripts/set_db_env.sh

# -----------------------------------------------------------
# Clean up to keep image small
# -----------------------------------------------------------
RUN rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# -----------------------------------------------------------
# Runtime
# -----------------------------------------------------------
EXPOSE 8000
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
