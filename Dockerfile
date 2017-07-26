FROM php:7.1-alpine

WORKDIR /sygic-travel/bot

RUN mkdir ./log && mkdir ./temp && chown -R www-data ./log ./temp

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
COPY composer.* ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-progress

# sources
COPY . /sygic-travel/bot

ENTRYPOINT ["php", "app/console.php"]
