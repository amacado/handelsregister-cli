FROM php:8.2-cli
RUN apt-get update && apt-get install -y \
        libzip-dev \
        zip \
        wget \
  && docker-php-ext-install zip

# Install google chrome
RUN wget -q https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb
RUN apt-get install -y ./google-chrome-stable_current_amd64.deb
RUN rm ./google-chrome-stable_current_amd64.deb

# Install dependencies for headless chromedriver
RUN apt-get install -y \
    libnss3-dev \
    libglib2.0-0 \
    libx11-xcb1

WORKDIR /app/src
VOLUME /app/storage

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Custom php ini settings
COPY /docker/production/handelsregister-cli-php.ini /usr/local/etc/php/conf.d/handelsregister-cli-php.ini

# Copy src files to container
COPY ./../../ /app/src

# Install dependencies
RUN composer install

# Install dusk driver
RUN php handelsregister-cli dusk:chrome-driver

# Build phar app and remove no longer required source files
RUN php handelsregister-cli app:build --no-interaction

RUN mv builds/handelsregister-cli /app/handelsregister-cli
RUN mkdir --parents /app/vendor/laravel/dusk/bin
RUN mv vendor/laravel/dusk/bin/chromedriver-linux /app/vendor/laravel/dusk/bin/
RUN rm -rf /app/src

ENTRYPOINT ["php", "/app/handelsregister-cli"]
