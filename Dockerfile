FROM php:7.2.2-fpm

# Mantainer Microservice-lumen image
MAINTAINER Fabrizio Cafolla info@fabriziocafolla.com

RUN buildDeps=" \
    " \
    runtimeDeps=" \
        curl \
        libxslt-dev \
        mysql-client \
        libfreetype6-dev \
        libjpeg-dev \
        unzip \
    " \
    && apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y $buildDeps $runtimeDeps\
    && docker-php-ext-install pdo_mysql xsl mbstring zip opcache pcntl\
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd \
    && apt-get purge -y --auto-remove $buildDeps \
    && rm -r /var/lib/apt/lists/*

# Run install git
RUN apt-get update && \
    apt-get install -y git

# Add file app in image
COPY application /var/www

# Set working directory as
WORKDIR /var/www

# Install Composer and remove it
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev \
    && rm -rf /usr/local/bin/composer*

# Install php redis
RUN printf "\n" | pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

# Copy env file
RUN cp .env.example .env \
    && php artisan jwt:secret -f

# Make permission to workdir
RUN chown -R www-data:www-data ./* \
    && chown -R www-data:www-data ./.* \
    && find . -type f -exec chmod 644 {} \; \
    && find . -type d -exec chmod 775 {} \;

EXPOSE 9000