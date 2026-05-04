# ---- Base PHP image ----
FROM php:8.5-cli-alpine

RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    bash \
    curl \
    tzdata \
    nodejs \
    npm && \
    docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    intl \
    bcmath \
    pcntl

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN npm install -g pnpm

WORKDIR /var/www/html

EXPOSE 8000