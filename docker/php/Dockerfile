FROM php:8.2-fpm

ARG WWWUSER=1000
ARG WWWGROUP=1000

# Instala dependências
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev

# Instala extensões PHP
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath

RUN pecl install --force apfd && rm -rf /tmp/pear && docker-php-ext-enable apfd

RUN groupadd -g ${WWWGROUP} appuser && \
    useradd -u ${WWWUSER} -g ${WWWGROUP} -m -s /bin/bash appuser

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura diretório de trabalho
WORKDIR /var/www/html

USER appuser

# Expor porta 9000 para o PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]