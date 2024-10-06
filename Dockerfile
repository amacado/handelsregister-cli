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

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

VOLUME /app
WORKDIR /app

# Prepare development environment
COPY ./docker/startup.sh /docker/startup.sh
ENTRYPOINT /docker/startup.sh
