FROM php:8.1.32-cli-alpine3.20

# Install system dependencies
RUN apk add --no-cache \
    git \
    unzip \
    curl \
    bash \
    $PHPIZE_DEPS

# Install Composer 2.8.4
COPY --from=composer:2.8.4 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Default command
CMD ["php", "-v"]


