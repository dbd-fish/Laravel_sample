FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリの作成
WORKDIR /var/www/app

# ホストのディレクトリが空の場合、新しいLaravelプロジェクトを作成
RUN if [ ! -f composer.json ]; then \
    composer create-project --prefer-dist laravel/laravel .; \
    else composer install; \
    fi

CMD php artisan serve --host=0.0.0.0 --port=8000
