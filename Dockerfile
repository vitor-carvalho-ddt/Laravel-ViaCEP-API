# Defining Local Arguments
ARG PHP_VERSION=latest
# Base image Latest
FROM php:${PHP_VERSION}

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
# git           -> to have git version controll tool installed
# curl          -> to have curl CLI tool installed
# libpng-dev    -> to have an interface for reading and writing .png files
# libonig-dev   -> Oniguruma is a library for working with regular expressions.
# libxml2-dev   -> A package that includes the development libraries for libxml2, an XML toolkit.
# zip           -> zip CLI tool
# unzip         -> unzip CLI tool
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \ 
    unzip

# Clear cache for reducing image size for build
# The path below is used for storing cache after package installation
# and they are no longer needed after it.
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP Extensions
# docker-php-ext-install  -> Easy installation of PHP extensions in official PHP Docker images (https://github.com/mlocati/docker-php-extension-installer)
# pdo_mysql               -> Is a driver that implements the PHP Data Objects (PDO) interface to enable access from PHP to MySQL databases.
# mbstring                -> Is a PHP extension that manages multibyte strings (characters that need more than one byte to be represented)
# exif                    -> With the exif extension you are able to work with image meta data.
# pcntl                   -> Process Control support in PHP implements the Unix style of process creation, program execution, signal handling and process termination.
# bcmath                  -> For arbitrary precision mathematics PHP offers BCMath which supports numbers of any size and precision up to 2147483647 (or 0x7FFFFFFF) decimal digits, if there is sufficient memory, represented as strings.
# gd                      -> PHP is not limited to creating just HTML output. It can also be used to create and manipulate image files in a variety of different image formats, including GIF, PNG, JPEG, WBMP, and XPM. Even more conveniently, PHP can output image streams directly to a browser. You will need to compile PHP with the GD library of image functions for this to work.
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer (dependency manager for PHP) - for composer.json and composer.lock dependencies
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan commands
# useradd → Creates a new user.
# -G www-data,root → Adds the user to the www-data and root groups.
# -u $uid → Assigns a specific user ID ($uid is a variable).
# -d /home/$user → Sets the home directory for the user.
# $user → The username (defined by the variable $user).
RUN useradd -G www-data,root -u $uid -d /home/$user $user

# mkdir -p /home/$user/.composer
# Creates the .composer directory inside the user's home (/home/$user).
# -p ensures parent directories are created if they don't exist.
# chown -R $user:$user /home/$user
# Changes the ownership of /home/$user to $user.
# -R makes it recursive, applying ownership to all files inside.
# When using PHP Composer, it stores cache, auth tokens, and configs in ~/.composer/.
# Without this directory, Composer might fail due to permission errors.
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# The WORKDIR instruction sets the working directory for any 
# RUN, CMD, ENTRYPOINT, COPY and ADD instructions that follow it in the Dockerfile.
WORKDIR /var/www

# The USER instruction sets the user name (or UID) and optionally 
# the user group (or GID) to use as the default user and     group 
USER $user