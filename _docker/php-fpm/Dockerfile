FROM php:8.2-fpm

WORKDIR /var/www

# Install packages
RUN apt-get update && apt-get upgrade -y && \
    apt-get install -y \
        git \
        zlib1g-dev \
        libzip-dev \
        libfreetype6-dev \
        libpng-dev \
        libjpeg-dev \
        libpq-dev \
        postgresql-client \
        tzdata \
        libicu-dev \
        pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Setting the time zone
ENV TZ=Europe/Berlin
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN docker-php-ext-install bcmath zip opcache pdo_pgsql intl

# Configure non-root user.
ARG PUID=1000
ARG PGID=1000

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy existing application directory permissions
COPY --chown=www:www . /var/www

# Change current user to www
USER www

# Expose port 9000
EXPOSE 9000

#start php-fpm server
CMD ["php-fpm"]
