FROM laravelphp/vapor:php82-arm

COPY . /var/task

# Install GD library and its dependencies
RUN apk update && apk add --no-cache \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd

# Copy Custom php.ini
COPY custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini
