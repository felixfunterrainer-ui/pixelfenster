FROM php:8.3-apache

RUN a2enmod rewrite \
    && sed -i 's/index.php/index.html index.php/' /etc/apache2/mods-enabled/dir.conf \
    && apt-get update \
    && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Install composer + PHP deps (PHPMailer) before copying the rest, for better layer caching
COPY composer.json ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader --no-interaction

COPY . .

EXPOSE 80
