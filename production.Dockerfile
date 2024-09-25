FROM laravelphp/vapor:php82

COPY . /var/task

# Install GD library and its dependencies
RUN apk update && apk add --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd

# Install php Imagick Extension
RUN apk add imagemagick imagemagick-dev php82-pecl-imagick \
&& pecl install imagick \
&& docker-php-ext-enable imagick

# Copy Custom php.ini
COPY custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini
