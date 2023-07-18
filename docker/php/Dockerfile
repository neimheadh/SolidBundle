ARG FROM_IMAGE=php:fpm
FROM ${FROM_IMAGE}

# Install apt packages
RUN apt-get update \
 && apt-get upgrade -y \
 && apt-get install -y wget \
 && rm -rf /var/lib/apt/lists/*

# Install composer
ARG COMPOSER_VERSION=latest-stable
RUN wget "https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar" \
 && wget "https://getcomposer.org/download/${COMPOSER_VERSION}/composer.phar.sha256sum" \
 && sha256sum -c composer.phar.sha256sum \
 && rm composer.phar.sha256sum \
 && mv composer.phar /usr/local/bin/composer \
 && chmod +x /usr/local/bin/composer \
 && composer --version

# Install symfony
RUN wget wget https://get.symfony.com/cli/installer -O - | bash \
 && mv ~/.symfony*/bin/symfony /usr/local/bin \
 && rm -Rf ~/.symfony \
 && symfony -V

# Install database drivers
RUN docker-php-ext-install pdo_mysql

# Install php modules
# RUN docker-php-ext-configure gd --with-jpeg \
# && docker-php-ext-install exif gd intl opcache zip sockets sysvsem calendar

# Install xdebug with coverage
ARG XDEBUG_VERSION=""
RUN pecl install xdebug \
 && docker-php-ext-enable xdebug \
 && echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Create developer user
ARG UID=1000
ARG GID=1000
RUN groupadd -g ${GID} developer \
 && useradd -g ${GID} -u ${UID} -m developer
USER developer
