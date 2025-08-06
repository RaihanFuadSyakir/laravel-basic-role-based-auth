
# Use official PHP image with Apache
FROM php:8.2-fpm


# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
	git \
	unzip \
	zip \
	curl \
	libzip-dev \
	libpng-dev \
	libonig-dev \
	libxml2-dev \
	libpq-dev
RUN docker-php-ext-install \
	pdo \
	pdo_mysql \
	pgsql \
	pdo_pgsql \
	mbstring \
	zip \
	exif \
	pcntl \
	bcmath \
	gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Expose port 80
EXPOSE 80
