FROM php:7.0-apache

WORKDIR /var/www

# Apache cnfiguration
COPY symfony3.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite

# PHP configuration
RUN apt-get update && apt-get install -y \
  git \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng12-dev \
  libc-client-dev libkrb5-dev \
  && docker-php-ext-install pdo_mysql \
  && docker-php-ext-install calendar \
  && docker-php-ext-configure imap --with-imap-ssl --with-kerberos \
  && docker-php-ext-install imap \
  && docker-php-ext-install zip \
  && docker-php-ext-install opcache \
  && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
  && docker-php-ext-install -j$(nproc) gd \
  && docker-php-ext-install mbstring
COPY php.ini /usr/local/etc/php/

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Install PHPUnit
RUN curl -Lo phpunit.phar https://phar.phpunit.de/phpunit.phar \
  && chmod +x phpunit.phar \
  && mv phpunit.phar /usr/local/bin/phpunit

RUN usermod -u 1000 www-data

EXPOSE 80
