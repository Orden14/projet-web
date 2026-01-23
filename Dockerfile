FROM php:8.3-fpm

# Installer les dépendances système
RUN apt-get update && apt-get install -y --no-install-recommends \
    bash \
    curl \
    git \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zlib1g-dev \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    netcat-traditional \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Configurer et installer les extensions PHP une par une
RUN docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/

RUN docker-php-ext-install -j4 gd intl mbstring zip pdo pdo_mysql opcache

# Copier la configuration PHP personnalisée
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer Node.js 20+ et Yarn
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y --no-install-recommends nodejs && \
    npm install -g yarn && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . .

# Installer dépendances PHP
RUN composer install --no-interaction --optimize-autoloader

# Installer dépendances Node et générer les assets
RUN yarn install && \
    yarn build

# Créer les répertoires nécessaires
RUN mkdir -p var/cache var/log && \
    chmod -R 777 var/

EXPOSE 9000

CMD ["php-fpm"]
