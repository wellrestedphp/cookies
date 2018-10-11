FROM php:7.2

RUN apt-get update \
  && apt-get -y install \
    git \
    unzip \
    wget \
    zip \
  && apt-get clean

# Install Composer.
RUN curl -sS https://getcomposer.org/installer | php -- \
  --filename=composer --install-dir=/usr/local/bin

# Install XDebug
RUN pecl install xdebug \
  && docker-php-ext-enable xdebug

# Add symlink for phpunit for easier running
RUN ln -s /usr/local/src/wellrested/vendor/bin/phpunit /usr/local/bin/phpunit

# Create a user
RUN useradd -ms /bin/bash wellrested
USER wellrested

WORKDIR /usr/local/src/wellrested
