FROM php:7.3-fpm
ARG user
ARG uid
USER root
# always run apt update when start and after add new source list, then clean up at end.
RUN apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    apt-get install -yqq \
	apt-utils \
	nginx \
	libzip-dev \
	libpng-dev \
	libjpeg-dev \
	libfreetype6-dev \
	libssl-dev \
	gnupg2 \
	git \
	zlib1g \
	libicu-dev \
	g++ \
	optipng \
	pngquant \
	gifsicle \
	libldap2-dev \
	dnsutils \
	libzip-dev \
	zip \
	unzip
RUN docker-php-ext-install zip && \
      php -m | grep -q 'zip' && \
      docker-php-ext-install bcmath mysqli pdo_mysql intl opcache ldap pdo_mysql exif mbstring gd && \
      pecl install -o -f redis && \
      rm -rf /tmp/pear && \
      docker-php-ext-enable redis

COPY ./opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

RUN useradd -G www-data,root -u $uid -d /home/$user $user
# Set permissions
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user && \
    chmod -R 755 /home/$user && \
    mkdir -p /var/www/vendor && \
    chown -R www-data:www-data /var/www/vendor && \
    chmod -R 775 /var/www/vendor &&\
	chown -R $user:www-data /var/www &&\
	chmod -R 775 /var/www 


# Ensure proper permissions during runtime
RUN chmod -R 775 /var/www  && \
	chown -R $user:www-data /var/www
   
USER $user
COPY composer.json composer.lock ./

RUN composer self-update &&\
	composer install --no-scripts --no-autoloader
COPY . .

# Dump autoload files
USER root
RUN composer dump-autoload --optimize && \
    chown -R $user:www-data /var/www/vendor && \
    chmod -R 775 /var/www/vendor

# setup node js source will be used later to install node js
RUN curl -sL https://deb.nodesource.com/setup_20.x -o nodesource_setup.sh && \
    sh ./nodesource_setup.sh && \
    apt-get install -y nodejs
RUN mv /var/www/routes/api.php /var/www/routes/api.php.backup
RUN touch /var/www/routes/api.php
RUN php artisan view:cache &&\
	php artisan config:cache &&\
	php artisan route:cache && \
	php artisan optimize
RUN mv /var/www/routes/api.php.backup /var/www/routes/api.php
USER $user
	








