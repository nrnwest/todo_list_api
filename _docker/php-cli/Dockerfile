FROM php:8.2-cli

WORKDIR /var/www

RUN apt-get update -y && \
    apt-get upgrade -y && \
    apt-get install -y \
        git \
        unzip \
        zlib1g-dev \
        libzip-dev \
        libfreetype6-dev \
        libpng-dev \
        libjpeg-dev \
        libpq-dev \
        procps \
        postgresql-client \
        tzdata \
    	libicu-dev \
    	pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Setting the time zone
ENV TZ=Europe/Berlin
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN docker-php-ext-install bcmath zip pcntl opcache pdo_pgsql intl \
    && echo 'memory_limit = 512M' > $PHP_INI_DIR/conf.d/php-memory-limits.ini

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  docker-php-ext-enable redis

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Install Composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
# Latest release
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

# Change current user to www
USER www
