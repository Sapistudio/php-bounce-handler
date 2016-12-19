FROM php:7.0

WORKDIR /var/www

# PHP configuration
RUN apt-get update && apt-get install -y \
  # imap instalation
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng12-dev \
  libc-client-dev libkrb5-dev \
  && docker-php-ext-configure imap --with-imap-ssl --with-kerberos \
  && docker-php-ext-install imap \
  # end imap instalation
  && docker-php-ext-install opcache 

COPY php.ini /usr/local/etc/php/

CMD php bin/phpspec run