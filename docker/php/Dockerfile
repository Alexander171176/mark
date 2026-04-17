FROM php:8.2-fpm

WORKDIR /var/www

# 1) System packages (build deps + runtime deps)
RUN apt-get update && apt-get install -y --no-install-recommends \
    bash \
    curl \
    wget \
    nano \
    unzip \
    ca-certificates \
    build-essential \
    default-mysql-client \
    nodejs \
    npm \
    \
    # PHP ext deps
    libxml2-dev \
    libssl-dev \
    libonig-dev \
    libzip-dev \
    \
    # Memcached deps
    libmemcached-dev \
    zlib1g-dev \
    libsasl2-dev \
    \
    # GD deps
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    libfreetype6-dev \
    \
    # Imagick deps
    imagemagick \
    libmagickwand-dev \
    \
    # Image optimizers (optional runtime tools)
    jpegoptim \
    optipng \
    gifsicle \
    webp \
    pngquant \
    && rm -rf /var/lib/apt/lists/*

# 2) SVGO (global)
RUN npm install -g svgo

# 3) PHP extensions (single, readable list)
# GD config must be before install
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install -j$(nproc) \
        bcmath \
        exif \
        gd \
        mbstring \
        pcntl \
        pdo_mysql \
        zip

# 4) Imagick
RUN pecl install imagick && docker-php-ext-enable imagick

# 5) Memcached (PHP extension)
RUN pecl install memcached && docker-php-ext-enable memcached

# 6) Redis (PHP extension)
RUN pecl install redis && docker-php-ext-enable redis

# 6) Composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

# 7) php.ini (твой файл)
COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/php.ini

# 8) Cleanup npm cache (как у тебя)
RUN npm cache clean --force && \
    rm -rf /root/.npm /root/.node-gyp
