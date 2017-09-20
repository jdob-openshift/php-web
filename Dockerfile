FROM php:7.0-apache
LABEL version="1"
LABEL release="1"
LABEL name="Red Hat OpenShift - PHP & Database Demo"

# Install dependencies
RUN docker-php-ext-install mysqli

WORKDIR /var/www/html
COPY . .
