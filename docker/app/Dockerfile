FROM php:7.4-apache

# ----------------------------------------------------------------
# Add Packages to Image
# ----------------------------------------------------------------
RUN apt-get update && apt-get install -y --no-install-recommends \
    apt-utils \
    nano

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN docker-php-ext-enable pdo pdo_mysql mysqli

# ----------------------------------------------------------------
# Install Weeding Helper Application
# ----------------------------------------------------------------

# copies application code to the directory that is
# set by $path_main in config.php
COPY . /var/www/html

# file overrides from /docker directory
COPY ./docker/app/style.css /var/www/html/style.css
COPY ./docker/app/config.php /var/www/html/config.php
COPY ./docker/app/scripts.php /var/www/html/scripts.php
COPY ./docker/app/copytwo.php /var/www/html/copytwo.php
COPY ./docker/app/innreach_check.php /var/www/html/innreach_check.php
COPY ./docker/app/upload.php /var/www/html/upload.php
COPY ./docker/app/ajaxCRUD.class.php /var/www/html/ajaxcrud/ajaxCRUD.class.php

# makes the file upload directories, path is defined according
# to the value of $secure_outside_path set in config.php
RUN mkdir /var/www/weeding \
    && mkdir /var/www/weeding/upload \
    && mkdir /var/www/weeding/prepped

RUN chmod 777 -R /var/www/weeding

# ----------------------------------------------------------------
# Ports to Expose on Container
# ----------------------------------------------------------------
EXPOSE 80
EXPOSE 443

CMD ["apachectl", "-D", "FOREGROUND"]
