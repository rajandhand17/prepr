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

# Modify ImageMagick policy.xml to allow write permissions for PDF
RUN find / -name "policy.xml"

# RUN sed -i 's/<policy domain="coder" rights="read" pattern="PDF" \/>/<policy domain="coder" rights="read|write" pattern="PDF" \/>/' /etc/ImageMagick-6/policy.xml

# Copy Custom php.ini
COPY custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini
