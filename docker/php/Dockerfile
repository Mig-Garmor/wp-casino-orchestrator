FROM wordpress:6.6-php8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*