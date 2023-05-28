FROM php:7.4-fpm

# Instala as dependências necessárias
RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli pdo_pgsql \
    && docker-php-ext-enable pdo pdo_mysql mysqli pdo_pgsql

CMD ["php-fpm"]